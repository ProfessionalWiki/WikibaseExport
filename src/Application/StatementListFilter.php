<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application;

use DateTimeImmutable;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Statement\StatementList;

class StatementListFilter {

	/**
	 * @param array<mixed, PropertyId> $propertyIds
	 */
	public function onlyPropertyIds( StatementList $statements, array $propertyIds ): StatementList {
		$filtered = new StatementList();

		foreach ( $statements->toArray() as $statement ) {
			if ( in_array( $statement->getPropertyId(), $propertyIds ) ) {
				$filtered->addStatement( $statement );
			}
		}

		return $filtered;
	}


}
