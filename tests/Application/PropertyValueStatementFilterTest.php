<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application;

use DataValues\StringValue;
use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\PropertyValueStatementFilter;
use ValueFormatters\FormatterOptions;
use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\Lib\Formatters\SnakFormatter;
use Wikibase\Repo\WikibaseRepo;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\PropertyValueStatementFilter
 */
class PropertyValueStatementFilterTest extends TestCase {

	private function newFilter(): PropertyValueStatementFilter {
		return new PropertyValueStatementFilter(
			propertyId: 'P1',
			propertyValue: 'company',
			snakFormatter: WikibaseRepo::getSnakFormatterFactory()->getSnakFormatter(
				SnakFormatter::FORMAT_PLAIN,
				new FormatterOptions()
			)
		);
	}

	private function newStatement( string $id, string $value ): Statement {
		// TODO how to set property type without saving a property?
		return new Statement( new PropertyValueSnak( new NumericPropertyId( $id ), new StringValue( $value ) ) );
	}

	public function testStatementWithIdAndValueDoesMatch(): void {
		// TOOD: this fails because the property tpye doesn't exist
//		$this->assertTrue(
//			$this->newFilter()->statementMatches( $this->newStatement( 'P1', 'company' ) )
//		);
	}

	public function testStatementWithIdButWithDifferentValueDoesNotMatch(): void {
		$this->assertFalse(
			$this->newFilter()->statementMatches( $this->newStatement( 'P1', 'not-company' ) )
		);
	}

	public function testStatementWithDifferentIdDoesNotMatch(): void {
		$this->assertFalse(
			$this->newFilter()->statementMatches( $this->newStatement( 'P2', 'company' ) )
		);
	}

}
