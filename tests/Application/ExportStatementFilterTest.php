<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application;

use PHPUnit\Framework\TestCase;
use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikibase\DataModel\Snak\PropertyNoValueSnak;
use Wikibase\DataModel\Snak\PropertySomeValueSnak;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\DataModel\Statement\StatementList;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\ExportStatementFilter
 */
class ExportStatementFilterTest extends TestCase {

	public function testFilterPropertiesOnEmptyList(): void {
		$this->assertEquals(
			new StatementList(),
			( new StatementListFilter() )->onlyPropertyIds( // TODO
				new StatementList(),
				[ new NumericPropertyId( 'P1' ), new NumericPropertyId( 'P3' ) ]
			)
		);
	}

	public function testFilterProperties(): void {
		$this->assertEquals(
			new StatementList(
				new Statement( new PropertyNoValueSnak( new NumericPropertyId( 'P3' ) ) ),
				new Statement( new PropertySomeValueSnak( new NumericPropertyId( 'P3' ) ) ),
			),
			( new StatementListFilter() )->onlyPropertyIds( // TODO
				new StatementList(
					new Statement( new PropertyNoValueSnak( new NumericPropertyId( 'P2' ) ) ),
					new Statement( new PropertySomeValueSnak( new NumericPropertyId( 'P2' ) ) ),
					new Statement( new PropertyNoValueSnak( new NumericPropertyId( 'P3' ) ) ),
					new Statement( new PropertySomeValueSnak( new NumericPropertyId( 'P3' ) ) ),
					new Statement( new PropertyNoValueSnak( new NumericPropertyId( 'P4' ) ) ),
				),
				[ new NumericPropertyId( 'P1' ), new NumericPropertyId( 'P3' ) ]
			)
		);
	}

}
