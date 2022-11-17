<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application\Export;

use DataValues\DataValue;
use DataValues\StringValue;
use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\Export\MappedStatement;
use ProfessionalWiki\WikibaseExport\Application\Export\StatementMapper;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikibase\DataModel\Snak\PropertyNoValueSnak;
use Wikibase\DataModel\Snak\PropertySomeValueSnak;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\Statement;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\Export\StatementMapper
 */
class StatementMapperTest extends TestCase {

	public function testMapsStringValue(): void {
		$mapper = new StatementMapper();

		$this->assertEquals(
			new MappedStatement( 'P1', 'foo' ),
			$mapper->mapStatement(
				new Statement( new PropertyValueSnak( new NumericPropertyId( 'P1' ), new StringValue( 'foo' ) ) )
			)
		);
	}

	public function testMapsSomeValueAndNoValueToEmptyString(): void {
		$mapper = new StatementMapper();

		$this->assertEquals(
			new MappedStatement( 'P1', '' ),
			$mapper->mapStatement(
				new Statement( new PropertyNoValueSnak( new NumericPropertyId( 'P1' ) ) )
			)
		);

		$this->assertEquals(
			new MappedStatement( 'P1', '' ),
			$mapper->mapStatement(
				new Statement( new PropertySomeValueSnak( new NumericPropertyId( 'P1' ) ) )
			)
		);
	}

	/**
	 * @dataProvider valueProvider
	 */
	public function testMapsValue( DataValue $value, string $expected ): void {
		$mapper = new StatementMapper();

		$this->assertEquals(
			new MappedStatement( 'P1', $expected ),
			$mapper->mapStatement(
				new Statement( new PropertyValueSnak( new NumericPropertyId( 'P1' ), $value ) )
			)
		);
	}

	public function valueProvider(): iterable {
		yield [
			new StringValue( '~[,,_,,]:3' ),
			'~[,,_,,]:3'
		];
		yield [
			new EntityIdValue( new ItemId( 'Q42' ) ),
			'Q42'
		];

		// TODO: test other types
	}

}
