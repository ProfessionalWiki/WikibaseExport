<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Services\Lookup\TermLookup;

class ProductionHeaderBuilder implements HeaderBuilder {

	public function __construct(
		private bool $useLabelsInHeaders,
		private TermLookup $termLookup,
		private string $languageCode
	) {
	}

	public function propertyIdToHeader( PropertyId $propertyId ): string {
		if ( $this->useLabelsInHeaders ) {
			$label = $this->termLookup->getLabel( $propertyId, $this->languageCode );

			if ( $label !== null ) {
				return $label;
			}
		}

		return $propertyId->getSerialization();
	}

}
