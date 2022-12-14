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

		if ( $this->shouldFilter() ) {
			$searchResult = $this->getFilteredSearchResult( $results );
		} else {
			$searchResult = $this->getUnfilteredSearchResult( $results );
		}

		$this->presenter->presentSearchResult( $searchResult );
	}

	private function shouldFilter(): bool {
		return $this->subjectFilterPropertyId !== null && $this->subjectFilterPropertyValue !== null;
	}

	/**
	 * @return TermSearchResult[]
	 */
	private function getSearchResults( string $text, string $languageCode ): array {
		if ( version_compare( MW_VERSION, '1.39.0', '>=' ) ) {
			return $this->entitySearchHelper->getRankedSearchResults(
				text: $text,
				languageCode: $languageCode,
				entityType: 'item',
				limit: 50,
				strictLanguage: false,
				profileContext: null
			);
		}

		return $this->entitySearchHelper->getRankedSearchResults(
			text: $text,
			languageCode: $languageCode,
			entityType: 'item',
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
		$searchResult = new SearchResult();
		$this->entityCriterion = $this->newEntityCriterion();

		foreach ( $results as $result ) {
			$entity = $this->entityLookup->getEntity( $result->getEntityId() );

			if ( $entity === null ) {
				continue;
			}

			if ( $this->entityMatches( $entity ) ) {
				$searchResult->add(
					$result->getEntityId()->getLocalPart(),
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
