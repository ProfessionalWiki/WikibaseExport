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

		while ( true ) {
			$entity = $this->entitySource->next();

			if ( $entity === null ) {
				break;
			}

			$this->presenter->presentEntity( $this->entityMapper->map( $entity ) );
		}
	}

}
