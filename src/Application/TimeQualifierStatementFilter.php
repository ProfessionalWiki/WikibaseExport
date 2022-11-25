<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application;

use DataValues\TimeValue;
use DateTimeImmutable;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\DataModel\Statement\StatementFilter;

class TimeQualifierStatementFilter implements StatementFilter {

	public function __construct(
		private TimeRange $timeRange,
		private TimeQualifierProperties $qualifierProperties
	) {
	}

	public function statementMatches( Statement $statement ): bool {
		$niceStatement = new NiceStatement( $statement );

		$pointInTime = $niceStatement->getQualifierValue( $this->qualifierProperties->pointInTime );

		if ( $pointInTime instanceof TimeValue ) {
			return $this->timeRange->contains( $this->timeValueToDateTimeImmutable( $pointInTime ) );
		}

		$startTime = $niceStatement->getQualifierValue( $this->qualifierProperties->startTime );
		$endTime = $niceStatement->getQualifierValue( $this->qualifierProperties->endTime );

		if ( $startTime instanceof TimeValue && $endTime instanceof TimeValue ) {
			return $this->qualifierRangeContainsTimeRange( $startTime, $endTime );
		}

		return false;
	}

	private function timeValueToDateTimeImmutable( TimeValue $timeValue ): DateTimeImmutable {
		return new DateTimeImmutable( str_replace( '-00', '-01', $timeValue->getTime() ) );
	}

	private function qualifierRangeContainsTimeRange( TimeValue $startTime, TimeValue $endTime ): bool {
		$qualifierRange = new TimeRange(
			start: $this->timeValueToDateTimeImmutable( $startTime ),
			end: $this->timeValueToDateTimeImmutable( $endTime )
		);

		return $qualifierRange->contains( $this->timeRange->start )
			|| $qualifierRange->contains( $this->timeRange->end )
			|| $this->timeRange->contains( $qualifierRange->start )
			|| $this->timeRange->contains( $qualifierRange->end );
	}

}
