<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\SearchEntities;

use ProfessionalWiki\WikibaseExport\Application\EntitySourceFactory;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Statement\StatementFilter;
use Wikibase\DataModel\Statement\StatementList;
use Wikibase\DataModel\Statement\StatementListProvider;
use Wikibase\Lib\Interactors\TermSearchResult;
use Wikibase\Repo\Api\EntitySearchHelper;

class SearchEntitiesUseCase {

	public function __construct(
		private bool $shouldFilterSubjects,
		private EntitySearchHelper $entitySearchHelper,
		private string $contentLanguage,
		private EntitySourceFactory $entitySourceFactory,
		private StatementFilter $subjectFilter,
		private SearchEntitiesPresenter $presenter
	) {
	}

	public function search( string $text ): void {
		// TOOD: check permission
		$results = $this->getSearchResults( $text );
		$this->filterSearchResults( $results );
	}

	/**
	 * @return TermSearchResult[]
	 */
	private function getSearchResults( string $text ): array {
		return $this->entitySearchHelper->getRankedSearchResults(
			text: $text,
			languageCode: $this->contentLanguage,
			entityType: 'item',
			// TODO: what is a sane limit for performance and UI?
			limit: 50,
			strictLanguage: false
		);
	}

	/**
	 * @param TermSearchResult[] $results
	 */
	private function filterSearchResults( array $results ): void {
		// TODO: maybe don't waste time retrieving entities when the filtering is not configured?
		// TODO: ID and label are already available
		$entitySource = $this->entitySourceFactory->newEntitySource(
			$this->getIdsFromSearchResults( $results )
		);

		while ( true ) {
			$entity = $entitySource->next();

			if ( $entity === null ) {
				break;
			}

			if ( $this->entityMatches( $entity ) ) {
				$this->presenter->presentEntity( $entity );
			}
		}
	}

	/**
	 * @param TermSearchResult[] $results
	 * @return EntityId[]
	 */
	private function getIdsFromSearchResults( array $results ): array {
		return array_map(
			fn( TermSearchResult $result ) => $result->getEntityId(),
			$results
		);
	}

	private function entityMatches( EntityDocument $entity ): bool {
		if ( !$this->shouldFilterSubjects ) {
			return true;
		}

		if ( $entity instanceof StatementListProvider ) {
			return !$entity->getStatements()->filter( $this->subjectFilter )->isEmpty();
		}

		return false;
	}

}
