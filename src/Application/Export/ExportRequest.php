<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

use ProfessionalWiki\WikibaseExport\Application\PropertyIdList;
use Wikibase\DataModel\Entity\EntityId;

class ExportRequest {

	/**
	 * @param EntityId[] $subjectIds
	 */
	public function __construct(
		public /* readonly */ string $languageCode,
		public /* readonly */ array $subjectIds,
		public /* readonly */ PropertyIdList $statementPropertyIds,
		public /* readonly */ int $startYear,
		public /* readonly */ int $endYear,
	) {
	}

}
