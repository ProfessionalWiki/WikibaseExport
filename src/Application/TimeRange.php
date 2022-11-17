<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application;

use DateTimeImmutable;

/**
 * @psalm-immutable
 */
class TimeRange {

	public function __construct(
		public /* readonly */ DateTimeImmutable $start,
		public /* readonly */ DateTimeImmutable $end
	) {
	}

	public function contains( DateTimeImmutable $time ): bool {
		return $time->getTimestamp() >= $this->start->getTimestamp()
			&& $time->getTimestamp() <= $this->end->getTimestamp();
	}

	public static function newFromStartAndEndYear( int $startYear, int $endYear ): self {
		$endTime = new DateTimeImmutable( ( $endYear + 1 ) . '-01-01' );

		return new self(
			start: new DateTimeImmutable( $startYear . '-01-01' ),
			end: $endTime->setTimestamp( $endTime->getTimestamp() - 1 )
		);
	}

}
