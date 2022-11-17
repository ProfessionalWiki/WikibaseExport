<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

/**
 * @psalm-immutable
 */
class MappedStatement {

	public function __construct(
		public /* readonly */ string $propertyId,
		public /* readonly */ string $mainValue
	) {
	}

}
