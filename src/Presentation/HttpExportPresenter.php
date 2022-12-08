<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Presentation;

use MediaWiki\Rest\Response;
use MediaWiki\Rest\ResponseFactory;
use MediaWiki\Rest\Stream;
use ProfessionalWiki\WikibaseExport\Application\Export\ColumnHeaders;
use ProfessionalWiki\WikibaseExport\Application\Export\ExportPresenter;
use ProfessionalWiki\WikibaseExport\Application\Export\MappedEntity;

class HttpExportPresenter implements ExportPresenter {

	private ?Response $response = null;

	public function __construct(
		private WideCsvPresenter $presenter,
		private ResponseFactory $responseFactory
	) {
	}

	public function presentExportStarted( ColumnHeaders $headers ): void {
		$this->presenter->presentExportStarted( $headers );
	}

	public function presentEntity( MappedEntity $entity ): void {
		$this->presenter->presentEntity( $entity );
	}

	public function presentInvalidRequest(): void {
		$this->response = $this->responseFactory->createHttpError( 400 );
	}

	public function presentPermissionDenied(): void {
		$this->response = $this->responseFactory->createHttpError( 403 );
	}

	public function getResponse(): Response {
		if ( $this->response === null ) {
			$this->response = $this->createFileResponse();
		}

		return $this->response;
	}

	private function createFileResponse(): Response {
		$response = $this->responseFactory->create();
		$response->setHeader( 'Content-Disposition', 'attachment; filename=export.csv;' );
		$response->setHeader( 'Content-Type', 'text/csv' );
		$response->setBody( new Stream( $this->presenter->getStream() ) );

		return $response;
	}

}
