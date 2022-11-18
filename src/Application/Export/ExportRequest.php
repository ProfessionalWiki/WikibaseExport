<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Entity\PropertyId;

class ExportRequest {

	/**
	 * @param EntityId[] $subjectIds
	 * @param PropertyId[] $statementPropertyIds
	 */
	public function __construct(
		public /* readonly */ array $subjectIds,
		public /* readonly */ array $statementPropertyIds,
		public /* readonly */ int $startYear,
		public /* readonly */ int $endYear,
	) {
	}

}
