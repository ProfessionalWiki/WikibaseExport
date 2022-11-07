<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Presentation;

use ProfessionalWiki\WikibaseExport\Application\Export\ExportPresenter;
use ProfessionalWiki\WikibaseExport\Application\Export\ExportResponse;

class NullPresenter implements ExportPresenter {

	public function present( ExportResponse $response ): void {
	}

}
