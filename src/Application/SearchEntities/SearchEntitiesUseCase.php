<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\SearchEntities;

use DataValues\StringValue;
use ProfessionalWiki\WikibaseExport\Application\EntityCriterion;
use ProfessionalWiki\WikibaseExport\Application\StatementEqualityCriterion;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikibase\DataModel\Entity\StatementListProvidingEntity;
use Wikibase\DataModel\Services\Lookup\EntityLookup;
use Wikibase\Lib\Interactors\TermSearchResult;
use Wikibase\Repo\Api\EntitySearchHelper;

class SearchEntitiesUseCase {

	private EntityCriterion $entityCriterion;

	public function __construct(
		private ?string $subjectFilterPropertyId,
		private ?string $subjectFilterPropertyValue,
		private EntitySearchHelper $entitySearchHelper,
		private EntityLookup $entityLookup,
		private SearchEntitiesPresenter $presenter
	) {
	}

	public function search( string $text, string $languageCode ): void {
		// TODO: check permission
		$results = $this->getSearchResults( $text, $languageCode );

		$searchResult = $this->shouldFilter() 
			? $this->getFilteredSearchResult( $results )
			: $this->getUnfilteredSearchResult( $results );

		$this->presenter->presentSearchResult( $searchResult );
	}

	private function shouldFilter(): bool {
		return $this->subjectFilterPropertyId !== null && $this->subjectFilterPropertyValue !== null;
	}

	/**
	 * @return TermSearchResult[]
	 */
	private function getSearchResults( string $text, string $languageCode ): array {
		return $this->entitySearchHelper->getRankedSearchResults(
			text: $text,
			languageCode: $languageCode,
			entityType: 'item',
			limit: 50,
			strictLanguage: false,
			profileContext: null
		);
	}

	/**
	 * @param TermSearchResult[] $results
	 */
	private function getUnfilteredSearchResult( array $results ): SearchResult {
		$searchResult = new SearchResult();

		foreach ( $results as $result ) {
			$entityId = $result->getEntityId();
			if ( $entityId === null ) {
				continue;
			}
			
			$searchResult->add(
				$entityId->getSerialization(),
				$result->getDisplayLabel()?->getText() ?? ''
			);
		}

		return $searchResult;
	}

	/**
	 * @param TermSearchResult[] $results
	 */
	private function getFilteredSearchResult( array $results ): SearchResult {
		$searchResult = new SearchResult();
		$this->entityCriterion = $this->newEntityCriterion();

		foreach ( $results as $result ) {
			$entityId = $result->getEntityId();
			if ( $entityId === null ) {
				continue;
			}
			
			$entity = $this->entityLookup->getEntity( $entityId );

			if ( $entity === null ) {
				continue;
			}

			if ( $this->entityMatches( $entity ) ) {
				$searchResult->add(
					$entityId->getSerialization(),
					$result->getDisplayLabel()?->getText() ?? ''
				);
			}
		}

		return $searchResult;
	}

	private function entityMatches( EntityDocument $entity ): bool {
		if ( $entity instanceof StatementListProvidingEntity ) {
			return $this->entityCriterion->matches( $entity );
		}

		return false;
	}

	private function newEntityCriterion(): StatementEqualityCriterion {
		return new StatementEqualityCriterion(
			propertyId: new NumericPropertyId( $this->subjectFilterPropertyId ?? '' ),
			expectedValue: new StringValue( $this->subjectFilterPropertyValue ?? '' )
		);
	}

}
