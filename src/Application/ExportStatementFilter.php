<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application;

use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Services\Statement\Filter\PropertySetStatementFilter;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\DataModel\Statement\StatementFilter;

class ExportStatementFilter implements StatementFilter {

	private StatementFilter $idFilter;
	private StatementFilter $timeFilter;

	/**
	 * @param PropertyId[] $propertyIds
	 */
	public function __construct(
		array $propertyIds,
		TimeRange $timeRange,
		TimeQualifierProperties $qualifierProperties
	) {
		$this->idFilter = new PropertySetStatementFilter(
			array_map(
				fn( PropertyId $id ) => (string)$id,
				$propertyIds
			)
		);

		$this->timeFilter = new TimeQualifierStatementFilter( $timeRange, $qualifierProperties );
	}

	public function statementMatches( Statement $statement ): bool {
		return $this->idFilter->statementMatches( $statement )
			&& $this->timeFilter->statementMatches( $statement );
	}

}
