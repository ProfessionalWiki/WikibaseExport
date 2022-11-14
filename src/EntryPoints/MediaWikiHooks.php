<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\EntryPoints;

use ProfessionalWiki\WikibaseExport\WikibaseExportExtension;
use Title;

class MediaWikiHooks {

	public static function onContentHandlerDefaultModelFor( Title $title, ?string &$model ): void {
		if ( WikibaseExportExtension::getInstance()->isConfigTitle( $title ) ) {
			$model = 'json'; // CONTENT_MODEL_JSON (string to make Psalm happy)
		}
	}

}
