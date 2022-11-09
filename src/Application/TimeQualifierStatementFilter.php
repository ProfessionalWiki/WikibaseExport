<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application;

use Wikibase\DataModel\Statement\Statement;
use Wikibase\DataModel\Statement\StatementFilter;

class TimeQualifierStatementFilter implements StatementFilter {

	public function __construct(
		private TimeRange $timeRange,
		private TimeQualifierProperties $qualifierProperties
	) {
	}

	public function statementMatches( Statement $statement ): bool {
		return false;
	}

}
