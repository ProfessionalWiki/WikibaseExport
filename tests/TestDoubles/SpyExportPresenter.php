<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\TestDoubles;

use ProfessionalWiki\WikibaseExport\Application\Export\ExportPresenter;
use ProfessionalWiki\WikibaseExport\Application\Export\MappedEntity;

class SpyExportPresenter implements ExportPresenter {

	/**
	 * @var array<int, MappedEntity>
	 */
	public array $presentedEntitiesById = [];

	public bool $presentedInvalidRequest = false;

	public function presentEntity( MappedEntity $entity ): void {
		$this->presentedEntitiesById[$entity->id] = $entity;
	}

	public function presentInvalidRequest(): void {
		$this->presentedInvalidRequest = true;
	}

	public function presentedEntitiesCount(): int {
		return count( $this->presentedEntitiesById );
	}

}
