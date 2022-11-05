<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application;

use Wikibase\DataModel\Entity\EntityDocument;

interface EntitySource {

	public function next(): ?EntityDocument;

}
