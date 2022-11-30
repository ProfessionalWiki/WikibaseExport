<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application\Export;

use DataValues\StringValue;
use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\Export\EntityMapper;
use ProfessionalWiki\WikibaseExport\Application\Export\StatementMapper;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\FakeSnakFormatter;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\StubStatementGrouper;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikibase\DataModel\Services\Statement\Filter\NullStatementFilter;
use Wikibase\DataModel\Services\Statement\Filter\PropertySetStatementFilter;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\DataModel\Statement\StatementFilter;
use Wikibase\DataModel\Statement\StatementList;
use Wikibase\DataModel\Term\Fingerprint;
use Wikibase\DataModel\Term\Term;
use Wikibase\DataModel\Term\TermList;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\Export\EntityMapper
 * @covers \ProfessionalWiki\WikibaseExport\Application\Export\MappedEntity
 * @covers \ProfessionalWiki\WikibaseExport\Application\Export\MappedStatement
 */
class EntityMapperTest extends TestCase {

	public function testMapsId(): void {
		$this->assertSame(
			'Q42',
			$this->newMapper()->map(
				new Item( new ItemId( 'Q42' ) )
			)->id
		);
	}

	private function newMapper( StatementFilter $statementFilter = null ): EntityMapper {
		return new EntityMapper(
			statementFilter: $statementFilter ?? new NullStatementFilter(),
			statementGrouper: new StubStatementGrouper(),
			statementMapper: new StatementMapper( new FakeSnakFormatter() ),
			contentLanguage: 'en'
		);
	}

	public function testUsesFilter(): void {
		$mapper = $this->newMapper( statementFilter: new PropertySetStatementFilter( [ 'P4', 'P2' ] ) );

		$this->assertEquals(
			[
				'P2' => [ '222' ],
				'P4' => [ '444' ],
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
			)->getYear( StubStatementGrouper::YEAR )->getAllValuesPerProperty()
		);
	}

	public function testMapsMainValues(): void {
		$this->assertEquals(
			[
				'P1' => [ 'foo1' ],
				'P2' => [ 'bar' ],
			],
			$this->newMapper()->map(
				new Item(
					id: new ItemId( 'Q42' ),
					statements: new StatementList(
						new Statement( new PropertyValueSnak( new NumericPropertyId( 'P1' ), new StringValue( 'foo1' ) ) ),
						//new Statement( new PropertyValueSnak( new NumericPropertyId( 'P1' ), new StringValue( 'foo2' ) ) ), // TODO
						new Statement( new PropertyValueSnak( new NumericPropertyId( 'P2' ), new StringValue( 'bar' ) ) ),
					)
				)
			)->getYear( StubStatementGrouper::YEAR )->getAllValuesPerProperty()
		);
	}

	public function testMapsLabel(): void {
		$this->assertSame(
			'Foo Bar',
			$this->newMapper()->map(
				new Item(
					id: new ItemId( 'Q42' ),
					fingerprint: new Fingerprint( new Termlist( [ new Term( 'en', 'Foo Bar' ) ] ) )
				)
			)->label
		);
	}

	// TODO: implement and test best rank filter?
	// TODO: test multiple values

}
