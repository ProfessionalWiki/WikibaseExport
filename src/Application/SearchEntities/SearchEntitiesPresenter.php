<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\SearchEntities;

use Wikibase\DataModel\Entity\EntityDocument;

interface SearchEntitiesPresenter {

	public function presentEntity( EntityDocument $entity ): void;

}
