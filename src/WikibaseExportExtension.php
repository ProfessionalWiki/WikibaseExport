<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport;

use ProfessionalWiki\WikibaseExport\EntryPoints\ExportApi;

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

	public static function getExportApiFactory(): ExportApi {
		return self::getInstance()->newExportApi();
	}

	private function newExportApi(): ExportApi {
		return new ExportApi();
	}

}
