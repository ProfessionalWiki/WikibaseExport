<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Persistence;

use MediaWiki\Permissions\Authority;
use ProfessionalWiki\WikibaseExport\Application\Export\ExportAuthorizer;

class AuthorityBasedExportAuthorizer implements ExportAuthorizer {

	public function __construct(
		private Authority $authority
	) {
	}

	public function canExport(): bool {
		return $this->authority->isAllowed( 'read' );
	}

}
