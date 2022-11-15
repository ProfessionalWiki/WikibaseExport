<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application;

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

	// TODO: test group with range qualifiers

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

}
