<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport;

/**
 * Top level factory for the WikibaseExportExtension extension
 */
class WikibaseExportExtension {

	public static function getInstance(): self {
		/** @var ?WikibaseExportExtension $instance */
		static $instance = null;
		$instance ??= new self();
		return $instance;
	}

}
