<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\TestDoubles;

use ProfessionalWiki\WikibaseExport\Application\Export\ColumnHeaders;
use ProfessionalWiki\WikibaseExport\Application\Export\ExportPresenter;
use ProfessionalWiki\WikibaseExport\Application\Export\MappedEntity;

class SpyExportPresenter implements ExportPresenter {

	/**
	 * @var array<int, MappedEntity>
	 */
	public array $presentedEntitiesById = [];

	public bool $presentedInvalidRequest = false;
	public bool $presentedPermissionDenied = false;

	public function presentEntity( MappedEntity $entity ): void {
		$this->presentedEntitiesById[$entity->id] = $entity;
	}

	public function presentInvalidRequest(): void {
		$this->presentedInvalidRequest = true;
	}

	public function presentPermissionDenied(): void {
		$this->presentedPermissionDenied = true;
	}

	public function presentExportStarted( ColumnHeaders $headers ): void {
	}

}
