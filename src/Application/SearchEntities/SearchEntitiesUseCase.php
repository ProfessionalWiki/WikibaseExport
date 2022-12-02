<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\SearchEntities;

use DataValues\StringValue;
use ProfessionalWiki\WikibaseExport\Application\Config;
use ProfessionalWiki\WikibaseExport\Application\EntityCriterion;
use ProfessionalWiki\WikibaseExport\Application\EntitySourceFactory;
use ProfessionalWiki\WikibaseExport\Application\StatementEqualityCriterion;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikibase\DataModel\Entity\StatementListProvidingEntity;
use Wikibase\DataModel\Term\LabelsProvider;
use Wikibase\DataModel\Term\TermList;
use Wikibase\Lib\Interactors\TermSearchResult;
use Wikibase\Repo\Api\EntitySearchHelper;

class SearchEntitiesUseCase {

	private EntityCriterion $entityCriterion;

	public function __construct(
		private Config $config,
		private EntitySearchHelper $entitySearchHelper,
		private string $contentLanguage,
		private EntitySourceFactory $entitySourceFactory,
		private SearchEntitiesPresenter $presenter
	) {
	}

	public function search( string $text ): void {
		// TOOD: check permission
		$results = $this->getSearchResults( $text );

		if ( $this->config->shouldFilterSubjects() ) {
			$searchResult = $this->getFilteredSearchResult( $results );
		} else {
			$searchResult = $this->getUnfilteredSearchResult( $results );
		}

		$this->presenter->presentSearchResult( $searchResult );
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
	private function getUnfilteredSearchResult( array $results ): SearchResult {
		$searchResult = new SearchResult();

		foreach ( $results as $result ) {
			$searchResult->add(
				$result->getEntityId()->getLocalPart(),
				$result->getDisplayLabel()?->getText() ?? ''
			);
		}

		return $searchResult;
	}

	/**
	 * @param TermSearchResult[] $results
	 */
	private function getFilteredSearchResult( array $results ): SearchResult {
		$entitySource = $this->entitySourceFactory->newEntitySource(
			$this->getIdsFromSearchResults( $results )
		);

		$this->entityCriterion = $this->newEntityCriterion();

		$searchResult = new SearchResult();

		while ( true ) {
			$entity = $entitySource->next();

			if ( $entity === null ) {
				break;
			}

			if ( $this->entityMatches( $entity ) ) {
				if ( $entity instanceof LabelsProvider ) {
					$searchResult->add(
						$entity->getId()?->getLocalPart() ?? '',
						$this->getEntityLabel( $entity->getLabels() )
					);
				}
			}
		}

		return $searchResult;
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
		if ( $entity instanceof StatementListProvidingEntity ) {
			return $this->entityCriterion->matches( $entity );
		}

		return false;
	}

	private function newEntityCriterion(): StatementEqualityCriterion {
		return new StatementEqualityCriterion(
			propertyId: new NumericPropertyId( $this->config->subjectFilterPropertyId ?? '' ),
			expectedValue: new StringValue( $this->config->subjectFilterPropertyValue ?? '' )
		);
	}

	private function getEntityLabel( TermList $termList ): string {
		// TODO: use WB language fallback handling
		$terms = $termList->toTextArray();
		$first = reset( $terms );

		if ( $first === false ) {
			return '';
		}

		return $first;
	}

}
