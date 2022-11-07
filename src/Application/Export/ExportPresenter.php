<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

interface ExportPresenter {

	public function present( ExportResponse $response ): void;

}
