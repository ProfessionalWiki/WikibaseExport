<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application;

use Wikibase\DataModel\Entity\StatementListProvidingEntity;

interface EntityCriterion {

	public function matches( StatementListProvidingEntity $entity ): bool;

}
