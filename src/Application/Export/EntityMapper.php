<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

use DateTimeImmutable;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\PropertyId;

class EntityMapper {

	/**
	 * @param array<int, PropertyId> $statementPropertyIds
	 */
	public function __construct(
		private array $statementPropertyIds,
		private DateTimeImmutable $startTime,
		private DateTimeImmutable $endTime,
	) {
	}

	public function map( EntityDocument $entity ): MappedEntity {
		return new MappedEntity(
			id: (string)$entity->getId()
		);
	}

}
