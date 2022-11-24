<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Presentation;

use MediaWiki\Rest\Response;
use MediaWiki\Rest\ResponseFactory;
use MediaWiki\Rest\Stream;
use ProfessionalWiki\WikibaseExport\Application\Export\ExportPresenter;
use ProfessionalWiki\WikibaseExport\Application\Export\MappedEntity;

class HttpExportPresenter implements ExportPresenter {

	private bool $isValid = true;

	public function __construct(
		private WideCsvPresenter $presenter,
		private ResponseFactory $responseFactory
	) {
	}

	public function presentEntity( MappedEntity $entity ): void {
		$this->presenter->presentEntity( $entity );
	}

	public function presentInvalidRequest(): void {
		$this->isValid = false;
	}

	public function getResponse(): Response {
		if ( !$this->isValid ) {
			return $this->responseFactory->createHttpError( 400 );
		}

		return $this->createFileResponse();
	}

	private function createFileResponse(): Response {
		$response = $this->responseFactory->create();
		$response->setHeader( 'Content-Disposition', 'attachment; filename=export.csv;' );
		$response->setHeader( 'Content-Type', 'text/csv' );
		$response->setBody( new Stream( $this->presenter->getStream() ) );

		return $response;
	}

}