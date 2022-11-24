<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application;

use ProfessionalWiki\WikibaseExport\Persistence\IdListEntitySource;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Services\Lookup\EntityLookup;

class EntitySourceFactory {

	public function __construct(
		private EntityLookup $lookup
	) {
	}

	/**
	 * @param EntityId[] $subjectIds
	 */
	public function newEntitySource( array $subjectIds ): EntitySource {
		return new IdListEntitySource(
			$this->lookup,
			$subjectIds
		);
	}

}
