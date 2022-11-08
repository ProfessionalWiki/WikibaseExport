<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

use ProfessionalWiki\WikibaseExport\Application\EntitySource;

class ExportUseCase {

	public function __construct(
		private EntitySource $entitySource,
		private EntityMapper $entityMapper,
		private ExportPresenter $presenter,
	) {
	}

	public function export(): void {
		// TODO: auth

		$this->presenter->present( $this->buildResponse() );
	}

	private function buildResponse(): ExportResponse {
		$response = new ExportResponse();

		while ( true ) {
			$entity = $this->entitySource->next();

			if ( $entity === null ) {
				break;
			}

			$response->add( $this->entityMapper->map( $entity ) );
		}

		return $response;
	}

}
