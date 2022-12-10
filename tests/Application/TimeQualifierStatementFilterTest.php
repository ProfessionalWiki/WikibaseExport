<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\TimeQualifierProperties;
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

	public function testDayPrecisionPointInTimeWithinRangeMatches(): void {
		$filter = new TimeQualifierStatementFilter(
			TimeHelper::newJan2000ToDec2005(),
			TimeHelper::newTimeQualifierProperties()
		);

		$this->assertTrue( $filter->statementMatches( TimeHelper::newPointInTimeStatement( day: '2000-01-01' ) ) );
		$this->assertTrue( $filter->statementMatches( TimeHelper::newPointInTimeStatement( day: '2005-12-31' ) ) );
	}

	public function testMonthPrecisionPointInTimeWithinRangeMatches(): void {
		$filter = new TimeQualifierStatementFilter(
			TimeHelper::newJan2000ToDec2005(),
			TimeHelper::newTimeQualifierProperties()
		);

		$statement = TimeHelper::newPointInTimeStatement( day: '2000-01-00' );

		$this->assertTrue( $filter->statementMatches( $statement ) );
	}

	public function testYearPrecisionPointInTimeWithinRangeMatches(): void {
		$filter = new TimeQualifierStatementFilter(
			TimeHelper::newJan2000ToDec2005(),
			TimeHelper::newTimeQualifierProperties()
		);

		$this->assertTrue( $filter->statementMatches( TimeHelper::newPointInTimeStatement( day: '2000-00-00' ) ) );
		$this->assertTrue( $filter->statementMatches( TimeHelper::newPointInTimeStatement( day: '2005-00-00' ) ) );
	}

	public function testDayPrecisionPointInTimeOutsideOfRangeDoesNotMatch(): void {
		$filter = new TimeQualifierStatementFilter(
			TimeHelper::newJan2000ToDec2005(),
			TimeHelper::newTimeQualifierProperties()
		);

		$this->assertFalse( $filter->statementMatches( TimeHelper::newPointInTimeStatement( day: '1999-12-31' ) ) );
		$this->assertFalse( $filter->statementMatches( TimeHelper::newPointInTimeStatement( day: '2006-01-01' ) ) );
	}

	public function testMonthPrecisionPointInTimeOutsideOfRangeDoesNotMatch(): void {
		$filter = new TimeQualifierStatementFilter(
			TimeHelper::newJan2000ToDec2005(),
			TimeHelper::newTimeQualifierProperties()
		);

		$statement = TimeHelper::newPointInTimeStatement( day: '2006-01-00' );

		$this->assertFalse( $filter->statementMatches( $statement ) );
	}

	public function testYearPrecisionPointInTimeOutsideOfRangeDoesNotMatch(): void {
		$filter = new TimeQualifierStatementFilter(
			TimeHelper::newJan2000ToDec2005(),
			TimeHelper::newTimeQualifierProperties()
		);

		$this->assertFalse( $filter->statementMatches( TimeHelper::newPointInTimeStatement( day: '1999-00-00' ) ) );
		$this->assertFalse( $filter->statementMatches( TimeHelper::newPointInTimeStatement( day: '2006-00-00' ) ) );
	}

	public function testRangeQualifiersContainingTheTimeRangeMatch(): void {
		$filter = new TimeQualifierStatementFilter(
			TimeHelper::newJan2000ToDec2005(),
			TimeHelper::newTimeQualifierProperties()
		);

		$statement = TimeHelper::newTimeRangeStatement( startYear: 1999, endYear: 2006 );

		$this->assertTrue( $filter->statementMatches( $statement ) );
	}

	public function testRangeQualifiersContainedByTheTimeRangeMatch(): void {
		$filter = new TimeQualifierStatementFilter(
			TimeHelper::newJan2000ToDec2005(),
			TimeHelper::newTimeQualifierProperties()
		);

		$this->assertTrue( $filter->statementMatches(
			TimeHelper::newTimeRangeStatement( startYear: 2001, endYear: 2004 )
		) );
	}

	public function testRangeQualifiersWithTheSameBoundsAsTheTimeRangeMatch(): void {
		$filter = new TimeQualifierStatementFilter(
			TimeHelper::newJan2000ToDec2005(),
			TimeHelper::newTimeQualifierProperties()
		);

		$this->assertTrue( $filter->statementMatches(
			TimeHelper::newTimeRangeStatement( startYear: 2000, endYear: 2005 )
		) );
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

	public function testRangeWithoutStartDateWithEndDateBelowUpperBoundDoesMatch(): void {
		$filter = new TimeQualifierStatementFilter(
			TimeHelper::newJan2000ToDec2005(),
			TimeHelper::newTimeQualifierProperties()
		);

		$this->assertTrue( $filter->statementMatches(
			TimeHelper::newTimeRangeStatement( startYear: null, endYear: 2001 )
		) );
	}

	public function testRangeWithoutStartDateWithEndDateAboveUpperBoundDoesMatch(): void {
		$filter = new TimeQualifierStatementFilter(
			TimeHelper::newJan2000ToDec2005(),
			TimeHelper::newTimeQualifierProperties()
		);

		$this->assertTrue( $filter->statementMatches(
			TimeHelper::newTimeRangeStatement( startYear: null, endYear: 2006 )
		) );
	}

	public function testRangeWithoutStartDateWithEndDateBelowLowerBoundDoesNotMatch(): void {
		$filter = new TimeQualifierStatementFilter(
			TimeHelper::newJan2000ToDec2005(),
			TimeHelper::newTimeQualifierProperties()
		);

		$this->assertFalse( $filter->statementMatches(
			TimeHelper::newTimeRangeStatement( startYear: null, endYear: 1999 )
		) );
	}

	public function testRangeWithoutEndDateWithStartDateAboveLowerBoundDoesMatch(): void {
		$filter = new TimeQualifierStatementFilter(
			TimeHelper::newJan2000ToDec2005(),
			TimeHelper::newTimeQualifierProperties()
		);

		$this->assertTrue( $filter->statementMatches(
			TimeHelper::newTimeRangeStatement( startYear: 2001, endYear: null )
		) );
	}

	public function testRangeWithoutEndDateWithStartDateBelowLowerBoundDoesMatch(): void {
		$filter = new TimeQualifierStatementFilter(
			TimeHelper::newJan2000ToDec2005(),
			TimeHelper::newTimeQualifierProperties()
		);

		$this->assertTrue( $filter->statementMatches(
			TimeHelper::newTimeRangeStatement( startYear: 1999, endYear: null )
		) );
	}

	public function testRangeWithoutEndDateWithStartDateAboveUpperBoundDoesNotMatch(): void {
		$filter = new TimeQualifierStatementFilter(
			TimeHelper::newJan2000ToDec2005(),
			TimeHelper::newTimeQualifierProperties()
		);

		$this->assertFalse( $filter->statementMatches(
			TimeHelper::newTimeRangeStatement( startYear: 2006, endYear: null )
		) );
	}

	public function testDoesNotMatchWhenQualifierIdsAreNull(): void {
		$filter = new TimeQualifierStatementFilter(
			TimeHelper::newJan2000ToDec2005(),
			new TimeQualifierProperties(
				pointInTime: null,
				startTime: null,
				endTime: null
			)
		);

		$this->assertFalse( $filter->statementMatches(
			TimeHelper::newTimeRangeStatement( startYear: 2000, endYear: 2005 )
		) );
	}

}
