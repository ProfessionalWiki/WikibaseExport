<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\EntryPoints;

use MediaWiki\Rest\RequestData;
use MediaWiki\Rest\ResponseInterface;
use MediaWiki\Tests\Rest\Handler\HandlerTestTrait;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\TimeHelper;
use ProfessionalWiki\WikibaseExport\Tests\WikibaseExportIntegrationTest;
use ProfessionalWiki\WikibaseExport\WikibaseExportExtension;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Statement\StatementList;

/**
 * @covers \ProfessionalWiki\WikibaseExport\EntryPoints\ExportApi
 * @group Database
 */
class ExportApiTest extends WikibaseExportIntegrationTest {
	use HandlerTestTrait;

	private const LEGAL_NAME_ID = 'P100';

	public function testEdgeToEdge(): void {
		$this->editConfigPage(
			'
{
    "startTimePropertyId": "P100",
    "endTimePropertyId": "P200",
    "pointInTimePropertyId": "' . TimeHelper::POINT_IN_TIME_ID . '"
}
'
		);

		$this->saveProperty( TimeHelper::POINT_IN_TIME_ID, 'time', 'Point in time' );
		$this->saveProperty( self::LEGAL_NAME_ID, 'string', 'Legal name' );

		$this->saveEntity(
			new Item(
				id: new ItemId( 'Q42' ),
				statements: new StatementList(
					TimeHelper::newPointInTimeStatement(
						day: '2022-11-24',
						pId: self::LEGAL_NAME_ID,
						value: 'Hello future'
					),
					TimeHelper::newPointInTimeStatement(
						day: '2023-01-01',
						pId: self::LEGAL_NAME_ID,
						value: 'Above upper bound'
					),
					TimeHelper::newPointInTimeStatement(
						day: '2020-12-30',
						pId: self::LEGAL_NAME_ID,
						value: 'Below lower bound'
					),
					TimeHelper::newPointInTimeStatement(
						day: '2021-01-01',
						pId: self::LEGAL_NAME_ID,
						value: 'Included lower bound'
					),
					TimeHelper::newPointInTimeStatement(
						day: '2022-12-31',
						pId: self::LEGAL_NAME_ID,
						value: 'Included upper bound'
					),
				)
			)
		);

		$response = $this->executeHandler(
			WikibaseExportExtension::exportApiFactory(),
			new RequestData( [
				'queryParams' => [
					'subject_ids' => 'Q42',
					'statement_property_ids' => self::LEGAL_NAME_ID . '|P2',
					'start_year' => 2021,
					'end_year' => 2022,
					'format' => 'csvwide'
				]
			] )
		);

		$this->assertSame( 200, $response->getStatusCode() );
		$this->assertSame( 'attachment; filename=export.csv;', $response->getHeaderLine( 'Content-Disposition' ) );

		$this->assertResponseHasContent(
			$response,
			<<<CSV
ID,"P100 2022","P100 2021","P2 2022","P2 2021"
Q42,"Hello future
Included upper bound","Included lower bound",,
CSV
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
					'statement_property_ids' => 'P1|P2',
					'start_year' => 2022,
					'end_year' => 2020,
					'format' => 'csvwide'
				]
			] )
		);

		$this->assertSame( 400, $response->getStatusCode() );
	}

}
