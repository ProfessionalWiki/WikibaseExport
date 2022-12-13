<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application\Export;

use DataValues\DataValue;
use DataValues\DecimalValue;
use DataValues\Geo\Values\GlobeCoordinateValue;
use DataValues\Geo\Values\LatLongValue;
use DataValues\MonolingualTextValue;
use DataValues\QuantityValue;
use DataValues\StringValue;
use DataValues\TimeValue;
use MediaWikiIntegrationTestCase;
use ProfessionalWiki\WikibaseExport\Application\Export\ProductionValueSetCreatorFactory;
use ProfessionalWiki\WikibaseExport\Application\Export\ValueSet;
use ProfessionalWiki\WikibaseExport\Application\Export\ValueSetCreator;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikibase\DataModel\Services\Lookup\InMemoryDataTypeLookup;
use Wikibase\DataModel\Snak\PropertyNoValueSnak;
use Wikibase\DataModel\Snak\PropertySomeValueSnak;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\DataModel\Statement\StatementList;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\Export\ProductionValueSetCreator
 */
class ProductionValueSetCreatorTest extends MediaWikiIntegrationTestCase {

	public function setUp(): void {
		parent::setUp();

		$this->setService(
			'WikibaseRepo.PropertyDataTypeLookup',
			$this->newDataTypeLookup()
		);
	}

	private function newDataTypeLookup(): InMemoryDataTypeLookup {
		$dataTypeLookup = new InMemoryDataTypeLookup();

		$types = [
			'P1' => 'string',
			'P2' => 'wikibase-item',
			'P3' => 'wikibase-property',
			'P4' => 'url',
			'P5' => 'external-id',
			'P6' => 'time',
			'P7' => 'quantity',
			'P8' => 'globe-coordinate',
			'P9' => 'monolingualtext',
		];

		foreach ( $types as $pId => $type ) {
			$dataTypeLookup->setDataTypeForProperty(
				new NumericPropertyId( $pId ),
				$type
			);
		}

		return $dataTypeLookup;
	}

	private function newValueSetCreator(): ValueSetCreator {
		return ( new ProductionValueSetCreatorFactory() )->newValueSetCreator( 'en' );
	}

	public function testHandlesEmptyStatementList(): void {
		$this->assertEquals(
			new ValueSet( [] ),
			$this->newValueSetCreator()->statementsToValueSet(
				new StatementList(
				)
			)
		);
	}

	public function testMapsStringValues(): void {
		$this->assertEquals(
			new ValueSet( [ 'foo', 'bar' ] ),
			$this->newValueSetCreator()->statementsToValueSet(
				new StatementList(
					new Statement( new PropertyValueSnak( new NumericPropertyId( 'P1' ), new StringValue( 'foo' ) ) ),
					new Statement( new PropertyValueSnak( new NumericPropertyId( 'P1' ), new StringValue( 'bar' ) ) ),
				)
			)
		);
	}

	public function testMapsSomeValueAndNoValueSnaks(): void {
		$this->assertEquals(
			new ValueSet( [ 'no value', 'unknown value' ] ),
			$this->newValueSetCreator()->statementsToValueSet(
				new StatementList(
					new Statement( new PropertyNoValueSnak( new NumericPropertyId( 'P1' ) ) ),
					new Statement( new PropertySomeValueSnak( new NumericPropertyId( 'P1' ) ) )
				)
			)
		);
	}

	/**
	 * @dataProvider valueProvider
	 */
	public function testMapsValue( string $pId, DataValue $value, string $expected ): void {
		$this->assertEquals(
			new ValueSet( [ $expected ] ),
			$this->newValueSetCreator()->statementsToValueSet(
				new StatementList(
					new Statement( new PropertyValueSnak( new NumericPropertyId( $pId ), $value ) )
				)
			)
		);
	}

	public function valueProvider(): iterable {
		yield [
			'P1',
			new StringValue( '~[,,_,,]:3' ),
			'~[,,_,,]:3'
		];
		yield [
			'P2',
			new EntityIdValue( new ItemId( 'Q42' ) ),
			'Q42'
		];
		yield [
			'P3',
			new EntityIdValue( new NumericPropertyId( 'P1' ) ),
			'P1'
		];
		yield [
			'P4',
			new StringValue( 'https://www.pro.wiki' ),
			'https://www.pro.wiki'
		];
		yield [
			'P5',
			new StringValue( 'FooBar' ),
			'FooBar'
		];
		yield [
			'P6',
			new TimeValue(
				'+2022-11-21T00:20:00Z',
				0,
				0,
				0,
				TimeValue::PRECISION_MINUTE,
				TimeValue::CALENDAR_GREGORIAN
			),
			'+2022-11-21T00:20:00Z'
		];
		yield [
			'P7',
			new QuantityValue( new DecimalValue( '+42' ), '1', new DecimalValue( '+52' ), new DecimalValue( '+32' ) ),
			'42±10'
		];
		yield [
			'P8',
			new GlobeCoordinateValue( new LatLongValue( 52.5200, 13.4050 ), 0.01 ),
			'52°31\'12"N, 13°24\'36"E'
		];
		yield [
			'P9',
			new MonolingualTextValue( 'en', 'FooBar Baz' ),
			'FooBar Baz'
		];
	}

}
