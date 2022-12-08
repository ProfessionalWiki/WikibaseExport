<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

class ValueSetList {

	/**
	 * @param ValueSet[] $sets
	 */
	public function __construct(
		public /* readonly */ array $sets
	) {
		foreach ( $this->sets as $set ) {
			if ( !( $set instanceof ValueSet ) ) {
				throw new \InvalidArgumentException();
			}
		}
	}

}
