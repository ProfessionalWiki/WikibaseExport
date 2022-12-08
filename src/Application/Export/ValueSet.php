<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

class ValueSet {

	/**
	 * @param string[] $values
	 */
	public function __construct(
		public /* readonly */ array $values
	) {
	}

}
