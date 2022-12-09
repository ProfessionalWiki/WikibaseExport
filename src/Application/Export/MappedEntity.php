<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

/**
 * @psalm-immutable
 */
class MappedEntity {

	public function __construct(
		public /* readonly */ string $id,
		public /* readonly */ string $label,
		public /* readonly */ ValueSetList $valueSetList
	) {
	}

}
