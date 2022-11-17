<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Persistence;

use ProfessionalWiki\WikibaseExport\Application\EntitySource;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Services\Lookup\EntityLookup;

class IdListEntitySource implements EntitySource {

	/**
	 * @var EntityId[]
	 */
	private array $subjectIds;

	/**
	 * @param EntityId[] $subjectIds
	 */
	public function __construct(
		private EntityLookup $entityLookup,
		array $subjectIds
	) {
		$this->subjectIds = $subjectIds;
	}

	public function next(): ?EntityDocument {
		while ( true ) {
			$id = array_shift( $this->subjectIds );

			if ( $id instanceof EntityId ) {
				$entity = $this->entityLookup->getEntity( $id );

				if ( $entity === null ) {
					continue;
				}

				return $entity;
			}

			return null;
		}
	}

}
