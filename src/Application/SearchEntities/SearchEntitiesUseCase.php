<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\SearchEntities;

use ProfessionalWiki\WikibaseExport\Application\Config;
use ProfessionalWiki\WikibaseExport\Application\EntitySourceFactory;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Services\Statement\Filter\PropertySetStatementFilter;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\DataModel\Statement\StatementFilter;
use Wikibase\DataModel\Statement\StatementList;
use Wikibase\DataModel\Statement\StatementListProvider;
use Wikibase\Lib\Formatters\SnakFormatter;
use Wikibase\Lib\Interactors\TermSearchResult;
use Wikibase\Repo\Api\EntitySearchHelper;

class SearchEntitiesUseCase {

	private StatementFilter $propertyIdFilter;

	public function __construct(
		private Config $config,
		private EntitySourceFactory $entitySourceFactory,
		private string $contentLangugae,
		private EntitySearchHelper $entitySearchHelper,
		private SnakFormatter $snakFormatter,
		private SearchEntitiesPresenter $presenter
	) {
	}

	public function search( string $text ): void {
		$results = $this->getSearchResults( $text );
		$this->filterSearchResults( $results );
	}

	/**
	 * @return TermSearchResult[]
	 */
	private function getSearchResults( string $text ): array {
		return $this->entitySearchHelper->getRankedSearchResults(
			text: $text,
			languageCode: $this->contentLangugae,
			entityType: 'item',
			limit: 50,
			strictLanguage: false
		);
	}

	/**
	 * @param TermSearchResult[] $results
	 */
	private function filterSearchResults( array $results ): void {
		$entitySource = $this->entitySourceFactory->newEntitySource(
			$this->getIdsFromSearchResults( $results )
		);

		// TOOD: maybe don't waste time retrieving entities when the filering is not configured

		$this->propertyIdFilter = new PropertySetStatementFilter( $this->config->subjectFilterPropertyId ?? '' );

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

	private function getStatements( EntityDocument $entity ): StatementList {
		if ( $entity instanceof StatementListProvider ) {
			return $entity->getStatements()->filter( $this->propertyIdFilter );
		}

		return new StatementList();
	}

	private function statementToString( Statement $statement ): string {
		return $this->snakFormatter->formatSnak( $statement->getMainSnak() );
	}

	private function entityMatches( EntityDocument $entity ): bool {
		// Filtering is not configured.
		if ( $this->config->subjectFilterPropertyId === null || $this->config->subjectFilterPropertyValue === null ) {
			return true;
		}

		$statements = $this->getStatements( $entity )->toArray();

		// Entity does not have a statement value.
		if ( count( $statements ) === 0 ) {
			return false;
		}

		foreach ( $statements as $statement ) {
			if ( $this->statementToString( $statement ) === $this->config->subjectFilterPropertyValue ) {
				return true;
			}
		}

		return false;
	}

}
