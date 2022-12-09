<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

class ColumnHeader {

	public function __construct(
		public /* readonly */ string $text
	) {
	}

}
