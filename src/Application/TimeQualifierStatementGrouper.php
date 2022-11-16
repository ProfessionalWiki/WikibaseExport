<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application;

use DateTimeImmutable;
use Wikibase\DataModel\Statement\StatementFilter;
use Wikibase\DataModel\Statement\StatementList;

class TimeQualifierStatementGrouper implements StatementGrouper {

	public static function newForYearRange( TimeQualifierProperties $timeQualifierProperties, int $startYear, int $endYear ): self {
		return new self(
			$timeQualifierProperties,
			range( $startYear, $endYear )
		);
	}

	/**
	 * @param int[] $years
	 */
	public function __construct(
		private TimeQualifierProperties $timeQualifierProperties,
		private array $years
	) {
	}

	/**
	 * @return array<int, StatementList>
	 */
	public function groupByYear( StatementList $statements ): array {
		$byYear = [];

		foreach ( $this->years as $year ) {
			$byYear[$year] = $statements->filter( $this->newFilter( $year ) );
			if ( $byYear[$year]->isEmpty() ) {
				unset( $byYear[$year] );
			}
		}

		return $byYear;
	}

	private function newFilter( int $year ): StatementFilter {
		return new TimeQualifierStatementFilter(
			timeRange: new TimeRange(
				start: new DateTimeImmutable( $year . '-01-01' ),
				end: new DateTimeImmutable( $year . '-12-31' ),
			),
			qualifierProperties: $this->timeQualifierProperties
		);
	}

}
