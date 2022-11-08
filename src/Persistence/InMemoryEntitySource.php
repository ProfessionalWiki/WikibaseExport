<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Persistence;

use ProfessionalWiki\WikibaseExport\Application\EntitySource;
use Wikibase\DataModel\Entity\EntityDocument;

class InMemoryEntitySource implements EntitySource {

	/**
	 * @var EntityDocument[]
	 */
	private array $entities;

	public function __construct( EntityDocument ...$entities ) {
		$this->entities = $entities;
	}

	public function next(): ?EntityDocument {
		return array_shift( $this->entities );
	}

}
