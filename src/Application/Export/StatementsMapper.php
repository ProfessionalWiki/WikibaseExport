<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

use Wikibase\DataModel\Statement\StatementList;

/**
 * Builds an ordered list of headers, and matching lists of ValueSet that correspond to rows in CSV.
 */
interface StatementsMapper {

	public function createColumnHeaders(): ColumnHeaders;

	public function buildValueSetList( StatementList $statements ): ValueSetList;

}
