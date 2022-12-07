<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application;

use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Services\Statement\Filter\PropertySetStatementFilter;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\DataModel\Statement\StatementFilter;

class ExportStatementFilter implements StatementFilter {

	private StatementFilter $basicIdFilter;
	private StatementFilter $timeIdFilter;
	private StatementFilter $timeQualifierFilter;

	/**
	 * @param PropertyId[] $alwaysIncludedProperties
	 * @param PropertyId[] $timeQualifiedProperties
	 */
	public function __construct(
		array $alwaysIncludedProperties,
		array $timeQualifiedProperties,
		TimeRange $timeRange,
		TimeQualifierProperties $qualifierProperties
	) {
		$this->basicIdFilter = $this->newPropertySetStatementFilter( $alwaysIncludedProperties );
		$this->timeIdFilter = $this->newPropertySetStatementFilter( $timeQualifiedProperties );
		$this->timeQualifierFilter = new TimeQualifierStatementFilter( $timeRange, $qualifierProperties );
	}

	/**
	 * @param PropertyId[] $ids
	 */
	private function newPropertySetStatementFilter( array $ids ): StatementFilter {
		return new PropertySetStatementFilter(
			array_map(
				fn( PropertyId $id ) => (string)$id,
				$ids
			)
		);
	}

	public function statementMatches( Statement $statement ): bool {
		return $this->isAlwaysIncluded( $statement )
			|| $this->isMatchingTimeQualified( $statement );
	}

	private function isAlwaysIncluded( Statement $statement ): bool {
		return $this->basicIdFilter->statementMatches( $statement );
	}

	private function isMatchingTimeQualified( Statement $statement ): bool {
		return $this->timeIdFilter->statementMatches( $statement )
			&& $this->timeQualifierFilter->statementMatches( $statement );
	}

}
