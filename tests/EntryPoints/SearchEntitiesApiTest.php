<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\EntryPoints;

use DataValues\StringValue;
use MediaWiki\Rest\RequestData;
use MediaWiki\Tests\Rest\Handler\HandlerTestTrait;
use MediaWiki\Tests\Unit\Permissions\MockAuthorityTrait;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\EntityHelper;
use ProfessionalWiki\WikibaseExport\Tests\WikibaseExportIntegrationTest;
use ProfessionalWiki\WikibaseExport\WikibaseExportExtension;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\DataModel\Statement\StatementList;

/**
 * @covers \ProfessionalWiki\WikibaseExport\EntryPoints\SearchEntitiesApi
 * @group Database
 */
class SearchEntitiesApiTest extends WikibaseExportIntegrationTest {
	use HandlerTestTrait;
	use MockAuthorityTrait;

	private const INSTANCE_OF_ID = 'P1';
	private const INSTANCE_OF_VALUE = 'company';
	private const SOMETHING_ELSE_ID = 'P2';

	public function setUp(): void {
		parent::setUp();

		$this->editConfigPage(
			'
{
    "subjectFilterPropertyId": "' . self::INSTANCE_OF_ID . '",
    "subjectFilterPropertyValue": "' . self::INSTANCE_OF_VALUE . '"
}
'
		);
	}

	private function newTextStatement( string $pId, string $value ): Statement {
		return new Statement(
			mainSnak: new PropertyValueSnak( new NumericPropertyId( $pId ), new StringValue( $value ) )
		);
	}

	public function testEdgeToEdge(): void {
		$this->skipOnPhp81AndLater();

		$this->saveProperty( self::INSTANCE_OF_ID, 'string', 'instance of' );

		$this->saveEntity(
			new Item(
				id: new ItemId( 'Q10' ),
				fingerprint: EntityHelper::newLabelFingerprint( 'Company Foo' ),
				statements: new StatementList(
					self::newTextStatement( self::INSTANCE_OF_ID, self::INSTANCE_OF_VALUE )
				)
			)
		);

		$this->saveEntity(
			new Item(
				id: new ItemId( 'Q12' ),
				fingerprint: EntityHelper::newLabelFingerprint( 'Company Foo Bar' ),
				statements: new StatementList(
					self::newTextStatement( self::INSTANCE_OF_ID, self::INSTANCE_OF_VALUE )
				)
			)
		);

		$this->saveEntity(
			new Item(
				id: new ItemId( 'Q15' ),
				fingerprint: EntityHelper::newLabelFingerprint( 'Business Foo' ),
				statements: new StatementList(
					self::newTextStatement( self::INSTANCE_OF_ID, self::INSTANCE_OF_VALUE )
				)
			)
		);

		$this->saveEntity(
			new Item(
				id: new ItemId( 'Q20' ),
				fingerprint: EntityHelper::newLabelFingerprint( 'Company Bar' ),
				statements: new StatementList(
					self::newTextStatement( self::INSTANCE_OF_ID, 'cat' )
				)
			)
		);

		$this->saveEntity(
			new Item(
				id: new ItemId( 'Q30' ),
				fingerprint: EntityHelper::newLabelFingerprint( 'Company Baz' ),
				statements: new StatementList(
					self::newTextStatement( self::SOMETHING_ELSE_ID, self::INSTANCE_OF_VALUE )
				)
			)
		);

		$this->saveEntity(
			new Item(
				id: new ItemId( 'Q40' ),
				fingerprint: EntityHelper::newLabelFingerprint( 'Company Without Statements' )
			)
		);

		$response = $this->executeHandler(
			WikibaseExportExtension::searchEntitiesApiFactory(),
			new RequestData( [
				'queryParams' => [
					'search' => 'Company'
				]
			] )
		);

		$this->assertSame( 200, $response->getStatusCode() );

		$this->assertSame(
			[
				'search' => [
					[
						'id' => 'Q10',
						'label' => 'Company Foo'
					],
					[
						'id' => 'Q12',
						'label' => 'Company Foo Bar'
					]
				]
			],
			json_decode( $response->getBody()->getContents(), true )
		);
	}

	public function testNoMatchesReturnsEmptyResult(): void {
		$response = $this->executeHandler(
			WikibaseExportExtension::searchEntitiesApiFactory(),
			new RequestData( [
				'queryParams' => [
					'search' => 'DefinitelyWillNotMatch',
				]
			] )
		);

		$this->assertSame(
			[
				'search' => []
			],
			json_decode( $response->getBody()->getContents(), true )
		);
	}

//	public function testUserWithoutPermissionReturns403(): void {
//		$response = $this->executeHandler(
//			WikibaseExportExtension::searchEntitiesApiFactory(),
//			new RequestData( [
//				'queryParams' => [
//					'search' => 'NoPermissionAnyway',
//				]
//			] ),
//			authority: $this->mockAnonNullAuthority()
//		);
//
//		$this->assertSame( 403, $response->getStatusCode() );
//	}

}
