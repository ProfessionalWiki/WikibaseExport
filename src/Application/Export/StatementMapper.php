<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

use Wikibase\DataModel\Statement\Statement;
use Wikibase\Lib\Formatters\SnakFormatter;

class StatementMapper {

	public function __construct(
		private SnakFormatter $snakFormatter
	) {
	}

	public function mapStatement( Statement $statement ): MappedStatement {
		return new MappedStatement(
			propertyId: $statement->getPropertyId()->getSerialization(),
			mainValue: $this->statementToString( $statement )
		);
	}

	private function statementToString( Statement $statement ): string {
		return $this->snakFormatter->formatSnak( $statement->getMainSnak() );
	}

}
