<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

use ProfessionalWiki\WikibaseExport\Application\StatementGrouper;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Statement\StatementFilter;
use Wikibase\DataModel\Statement\StatementList;
use Wikibase\DataModel\Statement\StatementListProvider;

class EntityMapper {

	public function __construct(
		private StatementFilter $statementFilter,
		private StatementGrouper $statementGrouper,
		private StatementMapper $statementMapper
	) {
	}

	public function map( EntityDocument $entity ): MappedEntity {
		return new MappedEntity(
			id: (string)$entity->getId(),
			statementsByYear: $this->buildYears( $entity )
		);
	}

	/**
	 * @return MappedYear[]
	 */
	private function buildYears( EntityDocument $entity ): array {
		$years = [];

		foreach ( $this->statementGrouper->groupByYear( $this->getFilteredStatements( $entity ) ) as $year => $statements ) {
			$years[] = new MappedYear(
				year: $year,
				statements: array_map(
					[ $this->statementMapper, 'mapStatement' ],
					$statements->toArray()
				)
			);
		}

		return $years;
	}

	private function getFilteredStatements( EntityDocument $entity ): StatementList {
		if ( $entity instanceof StatementListProvider ) {
			return $entity->getStatements()->filter( $this->statementFilter );
		}

		return new StatementList();
	}

}
