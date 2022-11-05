<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

use DateTimeImmutable;

class ExportRequest {

	/**
	 * @param array<int, string> $subjectIds
	 * @param array<int, string> $statementPropertyIds
	 */
	public function __construct(
		// TODO: stings or objects?
		// TODO: include subject IDs?
		public array $subjectIds,
		public array $statementPropertyIds,
		public ?DateTimeImmutable $startTime,
		public ?DateTimeImmutable $endTime,
	) {
	}

}
