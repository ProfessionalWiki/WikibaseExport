<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

use DataValues\DataValue;
use Wikibase\DataModel\Entity\EntityIdValue;
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
			return $this->valueToString( $snak->getDataValue() );
		}

		return ''; // TODO: empty string for NoValue and SomeValue?
	}

	private function valueToString( DataValue $value ): string {
		if ( $value instanceof EntityIdValue ) {
			return $value->getEntityId()->getSerialization();
		}

		/**
		 * @var string $serialization
		 */
		$serialization = $value->serialize();
		return $serialization; // TODO: maybe need to serialize in another manner
	}

}
