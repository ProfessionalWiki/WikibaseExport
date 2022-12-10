<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application;

use DataValues\DataValue;
use DataValues\TimeValue;
use DateTimeImmutable;
use LogicException;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\DataModel\Statement\StatementFilter;

class TimeQualifierStatementFilter implements StatementFilter {

	public function __construct(
		private TimeRange $timeRange,
		private TimeQualifierProperties $qualifierProperties
	) {
	}

	public function statementMatches( Statement $statement ): bool {
		$pointInTime = $this->getQualifierValue( $statement, $this->qualifierProperties->pointInTime );

		if ( $pointInTime instanceof TimeValue ) {
			return $this->timeRange->contains( $this->timeValueToDateTimeImmutable( $pointInTime ) );
		}

		$startTime = $this->getQualifierValue( $statement, $this->qualifierProperties->startTime );
		$endTime = $this->getQualifierValue( $statement, $this->qualifierProperties->endTime );

		if ( $startTime === null && $endTime === null ) {
			return false;
		}

		if ( ( $startTime === null || $startTime instanceof TimeValue )
			&& ( $endTime === null || $endTime instanceof TimeValue ) ) {
			return $this->qualifierRangeContainsTimeRange( $startTime, $endTime );
		}

		return false;
	}

	private function getQualifierValue( Statement $statement, ?PropertyId $propertyId ): ?DataValue {
		if ( $propertyId === null ) {
			return null;
		}

		return ( new NiceStatement( $statement ) )->getQualifierValue( $propertyId );
	}

	private function timeValueToDateTimeImmutable( TimeValue $timeValue ): DateTimeImmutable {
		return new DateTimeImmutable( str_replace( '-00', '-01', $timeValue->getTime() ) );
	}

	private function qualifierRangeContainsTimeRange( ?TimeValue $startTime, ?TimeValue $endTime ): bool {
		$qualifierRange = new TimeRange(
			start: $this->getStartTime( $startTime, $endTime ),
			end: $this->getEndTime( $startTime, $endTime )
		);

		return $qualifierRange->contains( $this->timeRange->start )
			|| $qualifierRange->contains( $this->timeRange->end )
			|| $this->timeRange->contains( $qualifierRange->start )
			|| $this->timeRange->contains( $qualifierRange->end );
	}

	private function getStartTime( ?TimeValue $startTime, ?TimeValue $endTime ): DateTimeImmutable {
		if ( $startTime !== null ) {
			return $this->timeValueToDateTimeImmutable( $startTime );
		}

		if ( $endTime === null ) {
			throw new LogicException( 'Statement without qualifier not supported' );
		}

		// When the qualifier start time is open-ended, allow any time not after the qualifier end time.
		$endDateTime = $this->timeValueToDateTimeImmutable( $endTime );
		if ( $this->timeRange->start <= $endDateTime ) {
			return $this->timeRange->start;
		}

		return $endDateTime;
	}

	private function getEndTime( ?TimeValue $startTime, ?TimeValue $endTime ): DateTimeImmutable {
		if ( $endTime !== null ) {
			return $this->timeValueToDateTimeImmutable( $endTime );
		}

		if ( $startTime === null ) {
			throw new LogicException( 'Statement without qualifier not supported' );
		}

		// When the qualifier end time is open-ended, allow any time not before the qualifier start time.
		$startDateTime = $this->timeValueToDateTimeImmutable( $startTime );
		if ( $this->timeRange->end >= $startDateTime ) {
			return $this->timeRange->end;
		}

		return $startDateTime;
	}

}
