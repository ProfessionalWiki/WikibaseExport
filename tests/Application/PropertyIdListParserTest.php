<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\PropertyIdListParser;
use Wikibase\DataModel\Entity\NumericPropertyId;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\PropertyIdListParser
 */
class PropertyIdListParserTest extends TestCase {

	public function testInvalidIdsAreDropped(): void {
		$this->assertEquals(
			[
				new NumericPropertyId( 'P4' ),
				new NumericPropertyId( 'P2' )
			],
			( new PropertyIdListParser() )->parse(
				[
					'Not an id',
					'P4',
					'Q1',
					'P2',
					'P42wrong'
				]
			)->ids
		);
	}

}
