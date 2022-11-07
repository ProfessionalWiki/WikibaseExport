<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

use ProfessionalWiki\WikibaseExport\Application\EntitySource;

class ExportUseCase {

	public function __construct(
		private ExportPresenter $presenter,
		private EntitySource $entitySource,
	) {
	}

	public function export( ExportRequest $exportRequest ): void {
		// TODO: auth

		$this->presenter->present( $this->buildResponse( $this->newEntityMapper( $exportRequest ) ) );
	}

	private function buildResponse( EntityMapper $mapper ): ExportResponse {
		$response = new ExportResponse();

		while ( true ) {
			$entity = $this->entitySource->next();

			if ( $entity === null ) {
				break;
			}

			$response->add( $mapper->map( $entity ) );
		}

		return $response;
	}

	private function newEntityMapper( ExportRequest $exportRequest ): EntityMapper {
		return new EntityMapper(
			statementPropertyIds: $exportRequest->statementPropertyIds,
			startTime: $exportRequest->startTime,
			endTime: $exportRequest->endTime
		);
	}

}
