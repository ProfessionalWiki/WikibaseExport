<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\TestDoubles;

use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Term\LabelsProvider;
use Wikibase\DataModel\Term\Term;
use Wikibase\Lib\Interactors\TermSearchResult;
use Wikibase\Repo\Api\EntitySearchHelper;

class StubEntitySearchHelper implements EntitySearchHelper {

	private const LANGUAGE = 'en';

	/**
	 * @var array<string, EntityDocument>
	 */
	private array $entityLabels = [];

	/**
	 * @param EntityDocument ...$entities
	 */
	public function __construct( ...$entities ) {
		foreach ( $entities as $entity ) {
			$this->addEntity( $entity );
		}
	}

	private function addEntity( EntityDocument $entity ): void {
		if ( $entity instanceof LabelsProvider ) {
			$labels = $entity->getLabels()->toTextArray();
			$label = reset( $labels );
			$this->entityLabels[$label] = $entity;
		}
	}

	/**
	 * @return TermSearchResult[]
	 */
	public function getRankedSearchResults( $text, $languageCode, $entityType, $limit, $strictLanguage ): array {
		$termResults = [];

		foreach ( $this->entityLabels as $label => $entity ) {
			$termResults[] = new TermSearchResult(
				matchedTerm: new Term( self::LANGUAGE, $text ),
				matchedTermType: 'match',
				entityId: $entity->getId(),
				displayLabel: new Term( self::LANGUAGE, $label )
			);
		}

		return $termResults;
	}

}
