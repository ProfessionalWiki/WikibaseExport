<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\TestDoubles;

use ProfessionalWiki\WikibaseExport\Application\Export\ExportPresenter;
use ProfessionalWiki\WikibaseExport\Application\Export\MappedEntity;

class SpyExportPresenter implements ExportPresenter {

	/**
	 * @var array<int, MappedEntity>
	 */
	private array $presentedEntitiesById = [];

	public function presentEntity( MappedEntity $entity ): void {
		$this->presentedEntitiesById[$entity->id] = $entity;
	}

	public function entityWasPresented( string $id ): bool {
		return array_key_exists( $id, $this->presentedEntitiesById );
	}

	public function presentedEntitiesCount(): int {
		return count( $this->presentedEntitiesById );
	}

}
