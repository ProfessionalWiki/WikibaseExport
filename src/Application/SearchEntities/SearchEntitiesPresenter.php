<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\SearchEntities;

interface SearchEntitiesPresenter {

	public function presentSearchResult( SearchResult $searchResult ): void;

}
