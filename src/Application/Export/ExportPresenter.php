<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

interface ExportPresenter {

	public function presentInvalidRequest(): void;

	public function presentPermissionDenied(): void;

	public function presentExportStarted( ColumnHeaders $headers ): void;

	public function presentEntity( MappedEntity $entity ): void;

}
