<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\TestDoubles;

use ProfessionalWiki\WikibaseExport\Application\StatementGrouper;
use Wikibase\DataModel\Statement\StatementList;

class StubStatementGrouper implements StatementGrouper {

	public const YEAR = 42;

	public function groupByYear( StatementList $statements ): array {
		return [ self::YEAR => $statements ];
	}

}
