<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

use Wikibase\DataModel\Entity\PropertyId;

interface HeaderBuilder {

	public function propertyIdToHeader( PropertyId $propertyId ): string;

}
