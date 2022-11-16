<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

class MappedYear {

	/**
	 * @param MappedStatement[] $statements
	 */
	public function __construct(
		public /* readonly */ int $year,
		public /* readonly */ array $statements
	) {
	}

}
