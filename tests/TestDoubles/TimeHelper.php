<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\TestDoubles;

use DataValues\StringValue;
use DataValues\TimeValue;
use DateTimeImmutable;
use ProfessionalWiki\WikibaseExport\Application\TimeQualifierProperties;
use ProfessionalWiki\WikibaseExport\Application\TimeRange;
use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikibase\DataModel\Snak\PropertyNoValueSnak;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Snak\SnakList;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\DataModel\Statement\StatementList;

class TimeHelper {

	public const START_TIME_ID = 'P40';
	public const END_TIME_ID = 'P41';
	public const POINT_IN_TIME_ID = 'P42';

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

	public static function newTimeRangeStatement( int $startYear, int $endYear ): Statement {
		return new Statement(
			mainSnak: new PropertyValueSnak( new NumericPropertyId( 'P1' ), new StringValue( 'Foo' ) ),
			qualifiers: new SnakList( [
				new PropertyValueSnak(
					new NumericPropertyId( self::START_TIME_ID ),
					self::newDay( '+' . $startYear . '-01-01T00:00:00Z' )
				),
				new PropertyValueSnak(
					new NumericPropertyId( self::END_TIME_ID ),
					self::newDay( '+' . $endYear . '-01-01T00:00:00Z' )
				)
			] )
		);
	}

	public static function newPointInTimeStatement( string $day, string $value = 'FooBar' ): Statement {
		return new Statement(
			mainSnak: new PropertyValueSnak( new NumericPropertyId( 'P1' ), new StringValue( $value ) ),
			qualifiers: new SnakList( [
				new PropertyValueSnak(
					new NumericPropertyId( self::POINT_IN_TIME_ID ),
					self::newDay( '+' . $day . 'T00:00:00Z' )
				)
			] )
		);
	}

}
