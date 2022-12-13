<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application;

use Wikibase\DataModel\Entity\EntityIdParsingException;
use Wikibase\DataModel\Entity\NumericPropertyId;

class PropertyIdListParser {

	/**
	 * Parses a list of strings to PropertyId objects,
	 * silently dropping everything that is not a valid property id.
	 *
	 * @param string[] $idStrings
	 * @throws EntityIdParsingException
	 */
	public function parse( array $idStrings ): PropertyIdList {
		$ids = [];

		foreach ( $idStrings as $idString ) {
			try {
				$ids[] = new NumericPropertyId( $idString );
			}
			catch ( \InvalidArgumentException ) {
			}
		}

		return new PropertyIdList( $ids );
	}

}
