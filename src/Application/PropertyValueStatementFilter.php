<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application;

use Wikibase\DataModel\Services\Statement\Filter\PropertySetStatementFilter;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\DataModel\Statement\StatementFilter;
use Wikibase\Lib\Formatters\SnakFormatter;

class PropertyValueStatementFilter implements StatementFilter {

	private StatementFilter $idFilter;

	public function __construct(
		string $propertyId,
		private string $propertyValue,
		private SnakFormatter $snakFormatter
	) {
		$this->idFilter = new PropertySetStatementFilter( [ $propertyId ] );
	}

	public function statementMatches( Statement $statement ): bool {
		return $this->idFilter->statementMatches( $statement ) && $this->valueMatches( $statement );
	}

	private function valueMatches( Statement $statement ): bool {
		return $this->statementToString( $statement ) === $this->propertyValue;
	}

	private function statementToString( Statement $statement ): string {
		return $this->snakFormatter->formatSnak( $statement->getMainSnak() );
	}

}
