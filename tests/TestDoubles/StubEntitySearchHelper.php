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
	private array $entities;

	/**
	 * @param EntityDocument ...$entities
	 */
	public function __construct( ...$entities ) {
		$this->entities = $entities;
	}

	/**
	 * @return TermSearchResult[]
	 */
	public function getRankedSearchResults( $text, $languageCode, $entityType, $limit, $strictLanguage ): array {
		$termResults = [];

		foreach ( $this->entities as $entity ) {
			$termResults[] = new TermSearchResult(
				matchedTerm: new Term( self::LANGUAGE, $text ),
				matchedTermType: 'match',
				entityId: $entity->getId(),
				displayLabel: new Term( self::LANGUAGE, $this->getEntityLabel( $entity ) )
			);
		}

		return $termResults;
	}

	private function getEntityLabel( EntityDocument $entity ): string {
		$labels = $entity->getLabels()->toTextArray();
		$label = reset( $labels );

		if ( $label === false ) {
			return '';
		}

		return $label;
	}

}
