<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application\Export;

use DataValues\StringValue;
use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\Export\EntityMapper;
use ProfessionalWiki\WikibaseExport\Application\Export\MappedEntity;
use ProfessionalWiki\WikibaseExport\Application\Export\MappedStatement;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikibase\DataModel\Services\Statement\Filter\NullStatementFilter;
use Wikibase\DataModel\Services\Statement\Filter\PropertySetStatementFilter;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\DataModel\Statement\StatementList;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\Export\EntityMapper
 * @covers \ProfessionalWiki\WikibaseExport\Application\Export\MappedEntity
 * @covers \ProfessionalWiki\WikibaseExport\Application\Export\MappedStatement
 */
class EntityMapperTest extends TestCase {

	public function testEmptyItem(): void {
		$mapper = new EntityMapper(
			statementFilter: new NullStatementFilter()
		);

		$this->assertEquals(
			new MappedEntity(
				id: 'Q42',
				statements: []
			),
			$mapper->map(
				new Item( new ItemId( 'Q42' ) )
			)
		);
	}

	public function testUsesFilter(): void {
		$mapper = new EntityMapper(
			statementFilter: new PropertySetStatementFilter( [ 'P4', 'P2' ] )
		);

		$this->assertEquals(
			[
				new MappedStatement( '222' ),
				new MappedStatement( '444' ),
			],
			$mapper->map(
				new Item(
					id: new ItemId( 'Q42' ),
					statements: new StatementList(
						new Statement( new PropertyValueSnak( new NumericPropertyId( 'P1' ), new StringValue( '111' ) ) ),
						new Statement( new PropertyValueSnak( new NumericPropertyId( 'P2' ), new StringValue( '222' ) ) ),
						new Statement( new PropertyValueSnak( new NumericPropertyId( 'P3' ), new StringValue( '333' ) ) ),
						new Statement( new PropertyValueSnak( new NumericPropertyId( 'P4' ), new StringValue( '444' ) ) ),
						new Statement( new PropertyValueSnak( new NumericPropertyId( 'P5' ), new StringValue( '555' ) ) ),
					)
				)
			)->statements
		);
	}

	public function testMapsMainValues(): void {
		$mapper = new EntityMapper(
			statementFilter: new NullStatementFilter()
		);

		$this->assertEquals(
			[
				new MappedStatement( 'foo1' ),
				new MappedStatement( 'bar' ),
			],
			$mapper->map(
				new Item(
					id: new ItemId( 'Q42' ),
					statements: new StatementList(
						new Statement( new PropertyValueSnak( new NumericPropertyId( 'P1' ), new StringValue( 'foo1' ) ) ),
						//new Statement( new PropertyValueSnak( new NumericPropertyId( 'P1' ), new StringValue( 'foo2' ) ) ), // TODO
						new Statement( new PropertyValueSnak( new NumericPropertyId( 'P2' ), new StringValue( 'bar' ) ) ),
					)
				)
			)->statements
		);
	}

	// TODO: test NoValue and SomeValue
	// TODO: implement and test best rank filter
	// TODO: test non-string values

}
