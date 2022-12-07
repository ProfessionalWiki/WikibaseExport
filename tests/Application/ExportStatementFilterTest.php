<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\ExportStatementFilter;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\TimeHelper;
use Wikibase\DataModel\Entity\NumericPropertyId;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\ExportStatementFilter
 */
class ExportStatementFilterTest extends TestCase {

	public function testStatementMatches(): void {
		$filter = new ExportStatementFilter(
			alwaysIncludedProperties: [ new NumericPropertyId( 'P1' ), new NumericPropertyId( 'P2' ) ],
			timeQualifiedProperties: [ new NumericPropertyId( 'P3' ), new NumericPropertyId( 'P4' ) ],
			timeRange: TimeHelper::newJan2000ToDec2005(),
			qualifierProperties: TimeHelper::newTimeQualifierProperties()
		);

		// In-range time qualified property
		$this->assertTrue( $filter->statementMatches( TimeHelper::newPointInTimeStatement( day: '2001-01-01', pId: 'P3' ) ) );

		// Out-of-range time qualified property
		$this->assertFalse( $filter->statementMatches( TimeHelper::newPointInTimeStatement( day: '1999-09-09', pId: 'P3' ) ) );

		// Always-included property with out-of-range qualifiers
		$this->assertTrue( $filter->statementMatches( TimeHelper::newPointInTimeStatement( day: '1999-09-09', pId: 'P2' ) ) );

		// Unknown property with in-range qualifiers
		$this->assertFalse( $filter->statementMatches( TimeHelper::newPointInTimeStatement( day: '2001-01-01', pId: 'P5' ) ) );
	}

}
