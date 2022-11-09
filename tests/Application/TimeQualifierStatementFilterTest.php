<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application;

use DataValues\TimeValue;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\TimeQualifierProperties;
use ProfessionalWiki\WikibaseExport\Application\TimeQualifierStatementFilter;
use ProfessionalWiki\WikibaseExport\Application\TimeRange;
use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikibase\DataModel\Snak\PropertyNoValueSnak;
use Wikibase\DataModel\Snak\PropertySomeValueSnak;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Snak\SnakList;
use Wikibase\DataModel\Statement\Statement;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\TimeQualifierStatementFilter
 */
class TimeQualifierStatementFilterTest extends TestCase {

	public const START_TIME_ID = 'P40';
	public const END_TIME_ID = 'P41';
	public const POINT_IN_TIME_ID = 'P42';

	public function testStatementWithoutQualifiersDoesNotMatch(): void {
		$filter = new TimeQualifierStatementFilter(
			$this->newJan2000ToDec2005(),
			$this->newTimeQualifierProperties()
		);

		$this->assertFalse(
			$filter->statementMatches( new Statement( new PropertySomeValueSnak( new NumericPropertyId( 'P10' ) ) ) )
		);
	}

	public static function newJan2000ToDec2005(): TimeRange {
		return new TimeRange(
			start: new DateTimeImmutable( '2000-01-01' ),
			end: new DateTimeImmutable( '2005-12-31' ),
		);
	}

	public static function newTimeQualifierProperties(): TimeQualifierProperties {
		return new TimeQualifierProperties(
			pointInTime: new NumericPropertyId( self::POINT_IN_TIME_ID ),
			startTime: new NumericPropertyId( self::START_TIME_ID ),
			endTime: new NumericPropertyId( self::END_TIME_ID ),
		);
	}

	public static function newDay( string $isoLikeTime ): TimeValue {
		return new TimeValue(
			$isoLikeTime,
			0,
			0,
			0,
			TimeValue::PRECISION_DAY,
			TimeValue::CALENDAR_GREGORIAN
		);
	}

	public function testPointInTimeWithinRangeMatches(): void {
		$filter = new TimeQualifierStatementFilter(
			$this->newJan2000ToDec2005(),
			$this->newTimeQualifierProperties()
		);

		$statement = new Statement(
			mainSnak: new PropertyNoValueSnak( new NumericPropertyId( 'P1' ) ),
			qualifiers: new SnakList( [
				new PropertyValueSnak(
					new NumericPropertyId( self::POINT_IN_TIME_ID ),
					self::newDay( '+2001-01-01T00:00:00Z' )
				)
			] )
		);

		$this->assertTrue( $filter->statementMatches( $statement ) );
	}

	public function testPointInTimeOutsideOfRangeDoesNotMatch(): void {
		$filter = new TimeQualifierStatementFilter(
			$this->newJan2000ToDec2005(),
			$this->newTimeQualifierProperties()
		);

		$statement = new Statement(
			mainSnak: new PropertyNoValueSnak( new NumericPropertyId( 'P1' ) ),
			qualifiers: new SnakList( [
				new PropertyValueSnak(
					new NumericPropertyId( self::POINT_IN_TIME_ID ),
					self::newDay( '+9042-01-01T00:00:00Z' )
				)
			] )
		);

		$this->assertFalse( $filter->statementMatches( $statement ) );
	}

	public function testRangeQualifiersContainingTheTimeRangeMatch(): void {
		$filter = new TimeQualifierStatementFilter(
			$this->newJan2000ToDec2005(),
			$this->newTimeQualifierProperties()
		);

		$statement = new Statement(
			mainSnak: new PropertyNoValueSnak( new NumericPropertyId( 'P1' ) ),
			qualifiers: new SnakList( [
				new PropertyValueSnak(
					new NumericPropertyId( self::START_TIME_ID ),
					self::newDay( '+1999-01-01T00:00:00Z' )
				),
				new PropertyValueSnak(
					new NumericPropertyId( self::END_TIME_ID ),
					self::newDay( '+2006-01-01T00:00:00Z' )
				)
			] )
		);

		$this->assertTrue( $filter->statementMatches( $statement ) );
	}

	public function testRangeQualifiersWithNoOverlapToTimeRangeDoNotMatch(): void {
		$filter = new TimeQualifierStatementFilter(
			$this->newJan2000ToDec2005(),
			$this->newTimeQualifierProperties()
		);

		$statement = new Statement(
			mainSnak: new PropertyNoValueSnak( new NumericPropertyId( 'P1' ) ),
			qualifiers: new SnakList( [
				new PropertyValueSnak(
					new NumericPropertyId( self::START_TIME_ID ),
					self::newDay( '+1990-01-01T00:00:00Z' )
				),
				new PropertyValueSnak(
					new NumericPropertyId( self::END_TIME_ID ),
					self::newDay( '+1999-01-01T00:00:00Z' )
				)
			] )
		);

		$this->assertFalse( $filter->statementMatches( $statement ) );
	}

}
