<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\TestDoubles;

use Wikibase\Lib\Interactors\TermSearchResult;
use Wikibase\Repo\Api\EntitySearchHelper;

class StubEntitySearchHelper implements EntitySearchHelper {

	/**
	 * @var TermSearchResult[]
	 */
	private array $searchResults;

	/**
	 * @param TermSearchResult ...$results
	 */
	public function __construct( ...$results ) {
		$this->searchResults = $results;
	}

	/**
	 * @return TermSearchResult[]
	 */
	public function getRankedSearchResults( $text, $languageCode, $entityType, $limit, $strictLanguage ): array {
		return $this->searchResults;
	}

}
