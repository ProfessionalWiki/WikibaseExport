<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\TimeQualifierStatementFilter;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\TimeHelper;
use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikibase\DataModel\Snak\PropertySomeValueSnak;
use Wikibase\DataModel\Statement\Statement;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\TimeQualifierStatementFilter
 */
class TimeQualifierStatementFilterTest extends TestCase {

	public function testStatementWithoutQualifiersDoesNotMatch(): void {
		$filter = new TimeQualifierStatementFilter(
			TimeHelper::newJan2000ToDec2005(),
			TimeHelper::newTimeQualifierProperties()
		);

		$this->assertFalse(
			$filter->statementMatches( new Statement( new PropertySomeValueSnak( new NumericPropertyId( 'P10' ) ) ) )
		);
	}

	public function testPointInTimeWithinRangeMatches(): void {
		$filter = new TimeQualifierStatementFilter(
			TimeHelper::newJan2000ToDec2005(),
			TimeHelper::newTimeQualifierProperties()
		);

		$statement = TimeHelper::newPointInTimeStatement( day: '2001-01-01' );

		$this->assertTrue( $filter->statementMatches( $statement ) );
	}

	public function testPointInTimeOutsideOfRangeDoesNotMatch(): void {
		$filter = new TimeQualifierStatementFilter(
			TimeHelper::newJan2000ToDec2005(),
			TimeHelper::newTimeQualifierProperties()
		);

		$statement = TimeHelper::newPointInTimeStatement( day: '9042-01-01' );

		$this->assertFalse( $filter->statementMatches( $statement ) );
	}

	public function testRangeQualifiersContainingTheTimeRangeMatch(): void {
		$filter = new TimeQualifierStatementFilter(
			TimeHelper::newJan2000ToDec2005(),
			TimeHelper::newTimeQualifierProperties()
		);

		$statement = TimeHelper::newTimeRangeStatement( startYear: 1999, endYear: 2006 );

		$this->assertTrue( $filter->statementMatches( $statement ) );
	}

	public function testRangeQualifiersWithNoOverlapToTimeRangeDoNotMatch(): void {
		$filter = new TimeQualifierStatementFilter(
			TimeHelper::newJan2000ToDec2005(),
			TimeHelper::newTimeQualifierProperties()
		);

		$statement = TimeHelper::newTimeRangeStatement( startYear: 1990, endYear: 1999 );

		$this->assertFalse( $filter->statementMatches( $statement ) );
	}

	public function testRangeWithOnlyLowerBoundIncludedDoesMatch(): void {
		$filter = new TimeQualifierStatementFilter(
			TimeHelper::newJan2000ToDec2005(),
			TimeHelper::newTimeQualifierProperties()
		);

		$this->assertTrue( $filter->statementMatches(
			TimeHelper::newTimeRangeStatement( startYear: 2004, endYear: 2022 )
		) );
	}

	public function testRangeWithOnlyUpperBoundIncludedDoesMatch(): void {
		$filter = new TimeQualifierStatementFilter(
			TimeHelper::newJan2000ToDec2005(),
			TimeHelper::newTimeQualifierProperties()
		);

		$this->assertTrue( $filter->statementMatches(
			TimeHelper::newTimeRangeStatement( startYear: 1990, endYear: 2001 )
		) );
	}

}
