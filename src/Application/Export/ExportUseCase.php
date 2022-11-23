<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

use ProfessionalWiki\WikibaseExport\Application\EntityMapperBuilder;
use ProfessionalWiki\WikibaseExport\Application\EntitySource;
use ProfessionalWiki\WikibaseExport\Application\EntitySourceBuilder;

class ExportUseCase {

	public function __construct(
		private EntitySourceBuilder $entitySourceBuilder,
		private EntityMapperBuilder $entityMapperBuilder,
		private ExportPresenter $presenter,
	) {
	}

	public function export( ExportRequest $request ): void {
		// TODO: validate request

		// TODO: auth

		$entitySource = $this->newEntitySource( $request );
		$entityMapper = $this->newEntityMapper( $request );

		while ( true ) {
			$entity = $entitySource->next();

			if ( $entity === null ) {
				break;
			}

			$this->presenter->presentEntity( $entityMapper->map( $entity ) );
		}
	}

	private function newEntitySource( ExportRequest $request ): EntitySource {
		return $this->entitySourceBuilder->newEntitySource( $request->subjectIds );
	}

	private function newEntityMapper( ExportRequest $request ): EntityMapper {
		return $this->entityMapperBuilder->newEntityMapper(
			$request->statementPropertyIds,
			$request->startYear,
			$request->endYear
		);
	}

}
