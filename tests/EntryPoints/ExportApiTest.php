<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\EntryPoints;

use DataValues\DecimalValue;
use DataValues\QuantityValue;
use DataValues\StringValue;
use MediaWiki\Rest\RequestData;
use MediaWiki\Rest\ResponseInterface;
use MediaWiki\Tests\Rest\Handler\HandlerTestTrait;
use MediaWiki\Tests\Unit\Permissions\MockAuthorityTrait;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\TimeHelper;
use ProfessionalWiki\WikibaseExport\Tests\WikibaseExportIntegrationTest;
use ProfessionalWiki\WikibaseExport\WikibaseExportExtension;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\DataModel\Statement\StatementList;

/**
 * @covers \ProfessionalWiki\WikibaseExport\EntryPoints\ExportApi
 * @group Database
 */
class ExportApiTest extends WikibaseExportIntegrationTest {
	use HandlerTestTrait;
	use MockAuthorityTrait;

	private const LEGAL_NAME_ID = 'P1';
	private const EMPLOYEE_COUNT_ID = 'P2';
	private const FOUNDER_NAME_ID = 'P4';

	public function setUp(): void {
		parent::setUp();

		$this->editConfigPage(
			'
{
    "startTimePropertyId": "' . TimeHelper::START_TIME_ID . '",
    "endTimePropertyId": "' . TimeHelper::END_TIME_ID . '",
    "pointInTimePropertyId": "' . TimeHelper::POINT_IN_TIME_ID . '",
	"propertiesToGroupByYear": ["' . self::LEGAL_NAME_ID . '", "' . self::EMPLOYEE_COUNT_ID . '"],
	"ungroupedProperties": ["P3", "' . self::FOUNDER_NAME_ID . '"]
}
'
		);
	}

	public function testEdgeToEdge(): void {
		$this->skipOnPhp81AndLater();

		$this->saveProperty( TimeHelper::START_TIME_ID, 'time', 'Start time' );
		$this->saveProperty( TimeHelper::END_TIME_ID, 'time', 'End time' );
		$this->saveProperty( TimeHelper::POINT_IN_TIME_ID, 'time', 'Point in time' );
		$this->saveProperty( self::LEGAL_NAME_ID, 'string', 'Legal name' );
		$this->saveProperty( self::EMPLOYEE_COUNT_ID, 'quantity', 'Revenue' );
		$this->saveProperty( self::FOUNDER_NAME_ID, 'string', 'Founded by' );

		$this->saveEntity(
			new Item(
				id: new ItemId( 'Q42' ),
				statements: new StatementList(
					TimeHelper::newPointInTimeStatement( '2022-11-24', self::LEGAL_NAME_ID, 'Hello future' ),
					TimeHelper::newPointInTimeStatement( '2023-01-01', self::LEGAL_NAME_ID, 'Above upper bound' ),
					TimeHelper::newPointInTimeStatement( '2020-12-30', self::LEGAL_NAME_ID, 'Below lower bound' ),
					TimeHelper::newPointInTimeStatement( '2021-01-01', self::LEGAL_NAME_ID, 'Included lower bound' ),
					TimeHelper::newPointInTimeStatement( '2022-12-31', self::LEGAL_NAME_ID, 'Included upper bound' ),
				)
			)
		);

		$this->saveEntity(
			new Item(
				id: new ItemId( 'Q43' ),
				statements: new StatementList(
					TimeHelper::newTimeRangeStatement( 2000, 2021, self::EMPLOYEE_COUNT_ID, $this->newQuantity( 1337 ) ),
					TimeHelper::newTimeRangeStatement( 2021, 2022, self::EMPLOYEE_COUNT_ID, $this->newQuantity( 5000 ) ),
					TimeHelper::newTimeRangeStatement( 2022, 2050, self::EMPLOYEE_COUNT_ID, $this->newQuantity( 9001 ) ),
					new Statement( new PropertyValueSnak(
						new NumericPropertyId( self::FOUNDER_NAME_ID ),
						new StringValue( 'Chuck Norris' )
					) )
				)
			)
		);

		$this->saveEntity( new Item( new ItemId( 'Q45' ) ) );

		$this->testTypicalResponse();
		$this->testLabelHeaders();
	}

	private function testTypicalResponse(): void {
		$response = $this->executeHandler(
			WikibaseExportExtension::exportApiFactory(),
			new RequestData( [
				'queryParams' => [
					'subject_ids' => 'Q42|Q43|Q44|Q45',
					'grouped_statement_property_ids' => implode( '|', [ self::LEGAL_NAME_ID, self::EMPLOYEE_COUNT_ID ] ),
					'ungrouped_statement_property_ids' => [ self::FOUNDER_NAME_ID ],
					'start_year' => 2021,
					'end_year' => 2022
				]
			] )
		);

		$this->assertSame( 200, $response->getStatusCode() );
		$this->assertSame( 'attachment; filename=export.csv;', $response->getHeaderLine( 'Content-Disposition' ) );

		$this->assertResponseHasContent(
			$response,
			<<<CSV
ID,Label,P4,"P1 2022","P1 2021","P2 2022","P2 2021"
Q42,,,"Hello future
Included upper bound","Included lower bound",,
Q43,,"Chuck Norris",,,"5,000±0 EUR
9,001±0 EUR","1,337±0 EUR
5,000±0 EUR"
Q45,,,,,,
CSV
		);
	}

	private function testLabelHeaders(): void {
		$response = $this->executeHandler(
			WikibaseExportExtension::exportApiFactory(),
			new RequestData( [
				'queryParams' => [
					'subject_ids' => '',
					'grouped_statement_property_ids' => implode( '|', [ self::LEGAL_NAME_ID, self::EMPLOYEE_COUNT_ID ] ),
					'ungrouped_statement_property_ids' => [ 'P3', self::FOUNDER_NAME_ID ],
					'start_year' => 2021,
					'end_year' => 2022,
					'header_type' => 'label'
				]
			] )
		);

		$this->assertResponseHasContent(
			$response,
			<<<CSV
ID,Label,P3,"Founded by","Legal name 2022","Legal name 2021","Revenue 2022","Revenue 2021"
CSV
		);
	}

	private function newQuantity( int $quantity ): QuantityValue {
		return new QuantityValue(
			new DecimalValue( $quantity ),
			'EUR',
			new DecimalValue( $quantity ),
			new DecimalValue( $quantity ),
		);
	}

	private function assertResponseHasContent( ResponseInterface $response, string $expected ): void {
		$response->getBody()->rewind();

		$this->assertSame(
			$expected,
			trim( $response->getBody()->getContents() )
		);
	}

	public function testInvalidRequestReturns400(): void {
		$response = $this->executeHandler(
			WikibaseExportExtension::exportApiFactory(),
			new RequestData( [
				'queryParams' => [
					'subject_ids' => 'Q1|Q2|Q3',
					'grouped_statement_property_ids' => 'P1|P2',
					'ungrouped_statement_property_ids' => [],
					'start_year' => 2022,
					'end_year' => 2020 // Lower end-year makes the request invalid
				]
			] )
		);

		$this->assertSame( 400, $response->getStatusCode() );
	}

	public function testUserWithoutPermissionReturns403(): void {
		$response = $this->executeHandler(
			WikibaseExportExtension::exportApiFactory(),
			new RequestData( [
				'queryParams' => [
					'subject_ids' => 'Q1|Q2|Q3',
					'grouped_statement_property_ids' => 'P1|P2',
					'ungrouped_statement_property_ids' => [],
					'start_year' => 2020,
					'end_year' => 2022
				]
			] ),
			authority: $this->mockAnonNullAuthority()
		);

		$this->assertSame( 403, $response->getStatusCode() );
	}

}
