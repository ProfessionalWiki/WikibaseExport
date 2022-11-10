<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application;

use DataValues\StringValue;
use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\NiceStatement;
use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikibase\DataModel\Snak\PropertyNoValueSnak;
use Wikibase\DataModel\Snak\PropertySomeValueSnak;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Snak\SnakList;
use Wikibase\DataModel\Statement\Statement;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\NiceStatement
 */
class NiceStatementTest extends TestCase {

	public function testGetQualifierValueFindsFirstMatch(): void {
		$statement = new Statement(
			mainSnak: new PropertyNoValueSnak( new NumericPropertyId( 'P42' ) ),
			qualifiers: new SnakList( [
				new PropertyValueSnak( // Wrong property ID
					new NumericPropertyId( 'P2' ),
					new StringValue( 'wrong' )
				),
				new PropertySomeValueSnak( // No DataValue
					new NumericPropertyId( 'P1' ),
				),
				new PropertyValueSnak( // Target result
					new NumericPropertyId( 'P1' ),
					new StringValue( 'foo' )
				),
				new PropertyValueSnak( // Not the first match
					new NumericPropertyId( 'P1' ),
					new StringValue( 'bar' )
				)
			] )
		);

		$this->assertEquals(
			new StringValue( 'foo' ),
			( new NiceStatement( $statement ) )->getQualifierValue( new NumericPropertyId( 'P1' ) )
		);
	}

	public function testGetQualifierValueReturnsNullWhenNothingMatches(): void {
		$statement = new Statement(
			mainSnak: new PropertyNoValueSnak( new NumericPropertyId( 'P42' ) ),
			qualifiers: new SnakList( [
				new PropertyValueSnak( // Wrong property ID
					new NumericPropertyId( 'P2' ),
					new StringValue( 'wrong' )
				),
				new PropertySomeValueSnak( // No DataValue
					new NumericPropertyId( 'P1' ),
				),
			] )
		);

		$this->assertNull(
			( new NiceStatement( $statement ) )->getQualifierValue( new NumericPropertyId( 'P1' ) )
		);
	}

}
