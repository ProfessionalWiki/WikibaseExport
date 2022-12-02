<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Presentation;

use MediaWiki\Rest\Response;
use MediaWiki\Rest\ResponseFactory;
use ProfessionalWiki\WikibaseExport\Application\SearchEntities\SearchEntitiesPresenter;
use ProfessionalWiki\WikibaseExport\Application\SearchEntities\SearchResult;

class HttpSearchEntitiesPresenter implements SearchEntitiesPresenter {

	private Response $response;

	public function __construct(
		private ResponseFactory $responseFactory
	) {
	}

	public function presentSearchResult( SearchResult $searchResult ): void {
		$this->response = $this->responseFactory->createJson( [
			'search' => $searchResult->toArray()
		] );
	}

	public function getResponse(): Response {
		return $this->response;
	}

}
