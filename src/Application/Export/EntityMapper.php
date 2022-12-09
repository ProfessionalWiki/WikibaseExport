<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Statement\StatementListProvider;
use Wikibase\DataModel\Term\LabelsProvider;
use Wikibase\DataModel\Term\TermList;

class EntityMapper {

	/**
	 * @param StatementsMapper[] $statementsMappers
	 */
	public function __construct(
		private string $languageCode,
		private array $statementsMappers
	) {
	}

	public function map( EntityDocument $entity ): MappedEntity {
		return new MappedEntity(
			id: (string)$entity->getId(),
			label: $this->getLabel( $entity ),
			valueSetList: $this->buildValueSetList( $entity )
		);
	}

	private function getLabels( EntityDocument $entity ): TermList {
		if ( $entity instanceof LabelsProvider ) {
			return $entity->getLabels();
		}

		return new TermList();
	}

	private function getLabel( EntityDocument $entity ): string {
		$labels = $this->getLabels( $entity );

		if ( $labels->hasTermForLanguage( $this->languageCode ) ) {
			return $labels->getByLanguage( $this->languageCode )->getText();
		}

		return '';
	}

	private function buildValueSetList( EntityDocument $entity ): ValueSetList {
		$lists = [];

		if ( $entity instanceof StatementListProvider ) {
			foreach ( $this->statementsMappers as $mapper ) {
				$lists[] = $mapper->buildValueSetList( $entity->getStatements() );
			}
		}

		return new ValueSetList(
			array_merge( ...array_map( fn( ValueSetList $l ) => $l->sets, $lists ) )
		);
	}

}
