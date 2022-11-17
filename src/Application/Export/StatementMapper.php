<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\Statement;

class StatementMapper {

	public function mapStatement( Statement $statement ): MappedStatement {
		return new MappedStatement(
			propertyId: $statement->getPropertyId()->getSerialization(),
			mainValue: $this->statementToString( $statement )
		);
	}

	private function statementToString( Statement $statement ): string {
		$snak = $statement->getMainSnak();

		if ( $snak instanceof PropertyValueSnak ) {
			/**
			 * @var string $serialization
			 */
			$serialization = $snak->getDataValue()->serialize();
			return $serialization; // TODO: maybe need to serialize in another manner
		}

		return ''; // TODO: empty string for NoValue and SomeValue?
	}

}
