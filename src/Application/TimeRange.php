<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application;

use DateTimeImmutable;

class TimeRange {

	public function __construct(
		public /* readonly */ DateTimeImmutable $start,
		public /* readonly */ DateTimeImmutable $end
	) {
	}

}
