<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\TestDoubles;

use ProfessionalWiki\WikibaseExport\Application\SearchEntities\SearchEntitiesPresenter;
use ProfessionalWiki\WikibaseExport\Application\SearchEntities\SearchResult;

class SpySearchEntitiesPresenter implements SearchEntitiesPresenter {

	/**
	 * @var array<array{id; string, label: string}>
	 */
	public array $searchResult = [];

	public function presentSearchResult( SearchResult $searchResult ): void {
		$this->searchResult = $searchResult->toArray();
	}

}
