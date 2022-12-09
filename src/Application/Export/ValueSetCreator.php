<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

use Wikibase\DataModel\Statement\StatementList;

interface ValueSetCreator {

	public function statementsToValueSet( StatementList $statements ): ValueSet;

}
