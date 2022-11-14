<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport;

use ProfessionalWiki\WikibaseExport\Application\Export\EntityMapper;
use ProfessionalWiki\WikibaseExport\EntryPoints\ExportApi;
use Title;

/**
 * Top level factory for the WikibaseExportExtension extension
 */
class WikibaseExportExtension {

	private const CONFIG_PAGE_TITLE = 'WikibaseExport';

	public static function getInstance(): self {
		/** @var ?WikibaseExportExtension $instance */
		static $instance = null;
		$instance ??= new self();
		return $instance;
	}

	public static function exportApiFactory(): ExportApi {
		return self::getInstance()->newExportApi();
	}

	private function newExportApi(): ExportApi {
		return new ExportApi();
	}

	public function isConfigTitle( Title $title ): bool {
		return $title->getNamespace() === NS_MEDIAWIKI
			&& $title->getText() === self::CONFIG_PAGE_TITLE;
	}

}
