<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

interface ExportAuthorizer {

	public function canExport(): bool;

}
