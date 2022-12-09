<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\PropertyIdList;
use Wikibase\DataModel\Entity\NumericPropertyId;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\PropertyIdList
 */
class PropertyIdListTest extends TestCase {

	public function testIntersect(): void {
		$a = new PropertyIdList( [
			new NumericPropertyId( 'P1' ),
			new NumericPropertyId( 'P2' ),
			new NumericPropertyId( 'P3' ),
			new NumericPropertyId( 'P4' ),
		] );

		$b = new PropertyIdList( [
			new NumericPropertyId( 'P10' ),
			new NumericPropertyId( 'P4' ),
			new NumericPropertyId( 'P11' ),
			new NumericPropertyId( 'P2' ),
			new NumericPropertyId( 'P12' ),
		] );

		$this->assertEquals(
			new PropertyIdList( [
				new NumericPropertyId( 'P4' ),
				new NumericPropertyId( 'P2' ),
			] ),
			$b->intersect( $a ) // Order of $b is retained
		);
	}

}
