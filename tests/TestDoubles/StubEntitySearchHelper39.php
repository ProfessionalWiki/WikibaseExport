<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\TestDoubles;

use Wikibase\Lib\Interactors\TermSearchResult;
use Wikibase\Repo\Api\EntitySearchHelper;

/**
 * Entity Search Helper compatible with Wikibase 1.39.
 */
class StubEntitySearchHelper39 implements EntitySearchHelper {

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
	public function getRankedSearchResults(
		$text,
		$languageCode,
		$entityType,
		$limit,
		$strictLanguage,
		?string $profileContext
	): array {
		return $this->searchResults;
	}

}
