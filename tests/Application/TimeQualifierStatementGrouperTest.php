<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application;

use DataValues\StringValue;
use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\TimeQualifierStatementGrouper;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\TimeHelper;
use Wikibase\DataModel\Statement\StatementList;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\TimeQualifierStatementGrouper
 */
class TimeQualifierStatementGrouperTest extends TestCase {

	public function testNoYearsResultInEmptyList(): void {
		$grouper = new TimeQualifierStatementGrouper(
			timeQualifierProperties: TimeHelper::newTimeQualifierProperties(),
			years: []
		);

		$this->assertSame(
			[],
			$grouper->groupByYear(
				new StatementList(
					TimeHelper::newTimeRangeStatement( startYear: 2022, endYear: 2022 )
				)
			)
		);
	}

	public function testNoStatementsResultInEmptyList(): void {
		$grouper = new TimeQualifierStatementGrouper(
			timeQualifierProperties: TimeHelper::newTimeQualifierProperties(),
			years: [ 2022, 2023 ]
		);

		$this->assertSame(
			[],
			$grouper->groupByYear(
				new StatementList()
			)
		);
	}

	public function testGroupsPointInTimeStatements(): void {
		$grouper = new TimeQualifierStatementGrouper(
			timeQualifierProperties: TimeHelper::newTimeQualifierProperties(),
			years: [ 2000, 2022, 2258, 2259, 2150 ]
		);

		$this->assertEquals(
			[
				2022 => new StatementList(
					TimeHelper::newPointInTimeStatement( day: '2022-11-15', value: 'Yesterday' ),
					TimeHelper::newPointInTimeStatement( day: '2022-11-16', value: 'Today' ),
				),
				2258 => new StatementList(
					TimeHelper::newPointInTimeStatement( day: '2258-01-01', value: 'Last best hope' )
				),
				2150 => new StatementList(
					TimeHelper::newPointInTimeStatement( day: '2150-12-07', value: 'Last day' ),
					TimeHelper::newPointInTimeStatement( day: '2150-12-08', value: '???' ),
				),
			],
			$grouper->groupByYear(
				new StatementList(
					TimeHelper::newPointInTimeStatement( day: '2022-11-15', value: 'Yesterday' ),
					TimeHelper::newPointInTimeStatement( day: '2150-12-07', value: 'Last day' ),
					TimeHelper::newPointInTimeStatement( day: '2022-11-16', value: 'Today' ),
					TimeHelper::newPointInTimeStatement( day: '2150-12-08', value: '???' ),
					TimeHelper::newPointInTimeStatement( day: '2258-01-01', value: 'Last best hope' ),
				)
			)
		);
	}

	public function testBuildFromYearRange(): void {
		$grouper = TimeQualifierStatementGrouper::newForYearRange(
			timeQualifierProperties: TimeHelper::newTimeQualifierProperties(),
			startYear: 2012,
			endYear: 2023
		);

		$this->assertEquals(
			[
				2012 => new StatementList(
					TimeHelper::newPointInTimeStatement( day: '2012-01-01' ),
				),
				2019 => new StatementList(
					TimeHelper::newPointInTimeStatement( day: '2019-01-01' ),
				),
				2022 => new StatementList(
					TimeHelper::newPointInTimeStatement( day: '2022-01-01' ),
					TimeHelper::newPointInTimeStatement( day: '2022-11-16' ),
				),
				2023 => new StatementList(
					TimeHelper::newPointInTimeStatement( day: '2023-01-01' ),
				),
			],
			$grouper->groupByYear(
				new StatementList(
					TimeHelper::newPointInTimeStatement( day: '2010-01-01' ),
					TimeHelper::newPointInTimeStatement( day: '2012-01-01' ),
					TimeHelper::newPointInTimeStatement( day: '2019-01-01' ),
					TimeHelper::newPointInTimeStatement( day: '2022-01-01' ),
					TimeHelper::newPointInTimeStatement( day: '2022-11-16' ),
					TimeHelper::newPointInTimeStatement( day: '2023-01-01' ),
					TimeHelper::newPointInTimeStatement( day: '2024-01-01' ),
				)
			)
		);
	}

	public function testGroupsTimeRangeStatements(): void {
		$grouper = new TimeQualifierStatementGrouper(
			timeQualifierProperties: TimeHelper::newTimeQualifierProperties(),
			years: [ 2000, 2022, 2258, 2259, 3000 ]
		);

		$this->assertEquals(
			[
				2022 => new StatementList(
					TimeHelper::newTimeRangeStatement( 2022, 2022, 'P1', new StringValue( 'One year' ) ),
					TimeHelper::newDayRangeStatement( '2021-12-31', '2022-01-01', 'P1', new StringValue( 'After lower bound' ) ),
					TimeHelper::newDayRangeStatement( '2022-12-31', '2023-01-01', 'P1', new StringValue( 'Before upper bound' ) ),
				),
				2258 => new StatementList(
					TimeHelper::newTimeRangeStatement( 2258, 2262, 'P1', new StringValue( 'B5' ) ),
					TimeHelper::newTimeRangeStatement( 2150, 3000, 'P1', new StringValue( 'Earth' ) )
				),
				2259 => new StatementList(
					TimeHelper::newTimeRangeStatement( 2258, 2262, 'P1', new StringValue( 'B5' ) ),
					TimeHelper::newTimeRangeStatement( 2150, 3000, 'P1', new StringValue( 'Earth' ) )
				),
				3000 => new StatementList(
					TimeHelper::newTimeRangeStatement( 2150, 3000, 'P1', new StringValue( 'Earth' ) )
				),
			],
			$grouper->groupByYear(
				new StatementList(
					TimeHelper::newTimeRangeStatement( 2022, 2022, 'P1', new StringValue( 'One year' ) ),
					TimeHelper::newTimeRangeStatement( 2258, 2262, 'P1', new StringValue( 'B5' ) ),
					TimeHelper::newTimeRangeStatement( 2150, 3000, 'P1', new StringValue( 'Earth' ) ),
					TimeHelper::newDayRangeStatement( '2021-12-31', '2021-12-31', 'P1', new StringValue( 'Before lower bound' ) ),
					TimeHelper::newDayRangeStatement( '2021-12-31', '2022-01-01', 'P1', new StringValue( 'After lower bound' ) ),
					TimeHelper::newDayRangeStatement( '2022-12-31', '2023-01-01', 'P1', new StringValue( 'Before upper bound' ) ),
					TimeHelper::newDayRangeStatement( '2023-01-01', '2023-01-01', 'P1', new StringValue( 'After upper bound' ) ),
				)
			)
		);
	}

	public function testGroupsTimeRangeStatementsWithoutStartDate(): void {
		$grouper = new TimeQualifierStatementGrouper(
			timeQualifierProperties: TimeHelper::newTimeQualifierProperties(),
			years: [ 2000, 2021, 2023 ]
		);

		$this->assertEquals(
			[
				2000 => new StatementList(
					TimeHelper::newTimeRangeStatement( null, 2022, 'P1', new StringValue( 'One year' ) ),
					TimeHelper::newTimeRangeStatement( null, 2023, 'P1', new StringValue( 'B5' ) ),
					TimeHelper::newDayRangeStatement( null, '2022-01-01', 'P1', new StringValue( 'Year with day' ) ),
					TimeHelper::newDayRangeStatement( null, '2023-01-01', 'P1', new StringValue( 'C5' ) )
				),
				2021 => new StatementList(
					TimeHelper::newTimeRangeStatement( null, 2022, 'P1', new StringValue( 'One year' ) ),
					TimeHelper::newTimeRangeStatement( null, 2023, 'P1', new StringValue( 'B5' ) ),
					TimeHelper::newDayRangeStatement( null, '2022-01-01', 'P1', new StringValue( 'Year with day' ) ),
					TimeHelper::newDayRangeStatement( null, '2023-01-01', 'P1', new StringValue( 'C5' ) )
				),
				2023 => new StatementList(
					TimeHelper::newTimeRangeStatement( null, 2023, 'P1', new StringValue( 'B5' ) ),
					TimeHelper::newDayRangeStatement( null, '2023-01-01', 'P1', new StringValue( 'C5' ) )
				),
			],
			$grouper->groupByYear(
				new StatementList(
					TimeHelper::newTimeRangeStatement( null, 2022, 'P1', new StringValue( 'One year' ) ),
					TimeHelper::newTimeRangeStatement( null, 2023, 'P1', new StringValue( 'B5' ) ),
					TimeHelper::newDayRangeStatement( null, '2022-01-01', 'P1', new StringValue( 'Year with day' ) ),
					TimeHelper::newDayRangeStatement( null, '2023-01-01', 'P1', new StringValue( 'C5' ) )
				)
			)
		);
	}

	public function testGroupsTimeRangeStatementsWithoutEndDate(): void {
		$grouper = new TimeQualifierStatementGrouper(
			timeQualifierProperties: TimeHelper::newTimeQualifierProperties(),
			years: [ 2000, 2021, 2023 ]
		);

		$this->assertEquals(
			[
				2000 => new StatementList(
					TimeHelper::newTimeRangeStatement( 2000, null, 'P1', new StringValue( 'One year' ) ),
					TimeHelper::newDayRangeStatement( '2000-01-01', null, 'P1', new StringValue( 'Year with day' ) ),
				),
				2021 => new StatementList(
					TimeHelper::newTimeRangeStatement( 2000, null, 'P1', new StringValue( 'One year' ) ),
					TimeHelper::newTimeRangeStatement( 2001, null, 'P1', new StringValue( 'B5' ) ),
					TimeHelper::newDayRangeStatement( '2000-01-01', null, 'P1', new StringValue( 'Year with day' ) ),
					TimeHelper::newDayRangeStatement( '2001-01-01', null, 'P1', new StringValue( 'C5' ) )
				),
				2023 => new StatementList(
					TimeHelper::newTimeRangeStatement( 2000, null, 'P1', new StringValue( 'One year' ) ),
					TimeHelper::newTimeRangeStatement( 2001, null, 'P1', new StringValue( 'B5' ) ),
					TimeHelper::newDayRangeStatement( '2000-01-01', null, 'P1', new StringValue( 'Year with day' ) ),
					TimeHelper::newDayRangeStatement( '2001-01-01', null, 'P1', new StringValue( 'C5' ) )
				),
			],
			$grouper->groupByYear(
				new StatementList(
					TimeHelper::newTimeRangeStatement( 2000, null, 'P1', new StringValue( 'One year' ) ),
					TimeHelper::newTimeRangeStatement( 2001, null, 'P1', new StringValue( 'B5' ) ),
					TimeHelper::newDayRangeStatement( '2000-01-01', null, 'P1', new StringValue( 'Year with day' ) ),
					TimeHelper::newDayRangeStatement( '2001-01-01', null, 'P1', new StringValue( 'C5' ) )
				)
			)
		);
	}

}
