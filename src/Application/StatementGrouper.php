<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application;

use Wikibase\DataModel\Statement\StatementList;

interface StatementGrouper {

	/**
	 * @return array<int, StatementList>
	 */
	public function groupByYear( StatementList $statements ): array;

}
