<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

use Wikibase\DataModel\Statement\StatementList;
use Wikibase\Lib\Formatters\SnakFormatter;

class ProductionValueSetCreator implements ValueSetCreator {

	public function __construct(
		private SnakFormatter $snakFormatter
	) {
	}

	public function statementsToValueSet( StatementList $statements ): ValueSet {
		$values = [];

		foreach ( $statements->getMainSnaks() as $snak ) {
			$values[] = $this->snakFormatter->formatSnak( $snak );
		}

		return new ValueSet( $values );
	}

}
