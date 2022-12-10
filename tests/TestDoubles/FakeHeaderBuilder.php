<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\TestDoubles;

use ProfessionalWiki\WikibaseExport\Application\Export\HeaderBuilder;
use Wikibase\DataModel\Entity\PropertyId;

class FakeHeaderBuilder implements HeaderBuilder {

	public function propertyIdToHeader( PropertyId $propertyId ): string {
		return $propertyId->getSerialization();
	}

}
