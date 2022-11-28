<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\TestDoubles;

use ProfessionalWiki\WikibaseExport\Application\Export\ExportAuthorizer;

class FailingExportAuthorizer implements ExportAuthorizer {

	public function canExport(): bool {
		return false;
	}

}
