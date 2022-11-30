<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

use Language;
use ProfessionalWiki\WikibaseExport\Application\StatementGrouper;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Statement\StatementFilter;
use Wikibase\DataModel\Statement\StatementList;
use Wikibase\DataModel\Statement\StatementListProvider;
use Wikibase\DataModel\Term\LabelsProvider;
use Wikibase\DataModel\Term\TermList;

class EntityMapper {

	public function __construct(
		private StatementFilter $statementFilter,
		private StatementGrouper $statementGrouper,
		private StatementMapper $statementMapper,
		private Language $contentLanguage
	) {
	}

	public function map( EntityDocument $entity ): MappedEntity {
		return new MappedEntity(
			id: (string)$entity->getId(),
			label: $this->getLabel( $entity ),
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

	private function getLabels( EntityDocument $entity ): TermList {
		if ( $entity instanceof LabelsProvider ) {
			return $entity->getLabels();
		}

		return new TermList();
	}

	private function getLabel( EntityDocument $entity ): string {
		$labels = $this->getLabels( $entity );

		if ( $labels->hasTermForLanguage( $this->contentLanguage->getCode() ) ) {
			return $labels->getByLanguage( $this->contentLanguage->getCode() )->getText();
		}

		return '';
	}

}
