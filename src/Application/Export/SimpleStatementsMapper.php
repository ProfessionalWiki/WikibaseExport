<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

use ProfessionalWiki\WikibaseExport\Application\PropertyIdList;
use Wikibase\DataModel\Statement\StatementList;

class SimpleStatementsMapper implements StatementsMapper {

	public function __construct(
		private ValueSetCreator $valueSetCreator,
		private PropertyIdList $propertyIds
	) {
	}

	public function createColumnHeaders(): ColumnHeaders {
		$headers = [];

		foreach ( $this->propertyIds->ids as $property ) {
			$headers[] = new ColumnHeader( $property->getSerialization() );
		}

		return new ColumnHeaders( $headers );
	}

	public function buildValueSetList( StatementList $statements ): ValueSetList {
		$valueSets = [];

		foreach ( $this->propertyIds->ids as $propertyId ) {
			$valueSets[] = $this->valueSetCreator->statementsToValueSet(
				$statements->getByPropertyId( $propertyId )->getBestStatements()
			);
		}

		return new ValueSetList( $valueSets );
	}

}
