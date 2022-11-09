<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\TimeQualifierProperties;
use ProfessionalWiki\WikibaseExport\Application\TimeQualifierStatementFilter;
use ProfessionalWiki\WikibaseExport\Application\TimeRange;
use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikibase\DataModel\Snak\PropertySomeValueSnak;
use Wikibase\DataModel\Statement\Statement;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\TimeQualifierStatementFilter
 */
class TimeQualifierStatementFilterTest extends TestCase {

	private const START_TIME_ID = 'P40';
	private const END_TIME_ID = 'P41';
	private const POINT_IN_TIME_ID = 'P42';

	public function testStatementWithoutQualifiersDoesNotMatch(): void {
		$filter = new TimeQualifierStatementFilter(
			$this->newJan2000ToDec2005(),
			$this->newTimeQualifierProperties()
		);

		$filter->statementMatches( new Statement( new PropertySomeValueSnak( new NumericPropertyId( 'P10' ) ) ) );
	}

	private function newJan2000ToDec2005(): TimeRange {
		return new TimeRange(
			start: new DateTimeImmutable( '2000-01-01' ),
			end: new DateTimeImmutable( '2005-12-31' ),
		);
	}

	private function newTimeQualifierProperties(): TimeQualifierProperties {
		return new TimeQualifierProperties(
			pointInTime: new NumericPropertyId( self::POINT_IN_TIME_ID ),
			startTime: new NumericPropertyId( self::START_TIME_ID ),
			endTime: new NumericPropertyId( self::END_TIME_ID ),
		);
	}

	public function testPointInTimeWithinRangeMatches(): void {
		$filter = new TimeQualifierStatementFilter(
			$this->newJan2000ToDec2005(),
			$this->newTimeQualifierProperties()
		);

		// TODO
		$filter->statementMatches( new Statement( new PropertySomeValueSnak( new NumericPropertyId( 'P10' ) ) ) );
	}

}
