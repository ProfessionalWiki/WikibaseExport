<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport;

use MediaWiki\MediaWikiServices;
use ProfessionalWiki\WikibaseExport\Application\Export\EntityMapper;
use ProfessionalWiki\WikibaseExport\EntryPoints\ExportApi;
use ProfessionalWiki\WikibaseExport\Persistence\CombiningConfigLookup;
use ProfessionalWiki\WikibaseExport\Persistence\ConfigDeserializer;
use ProfessionalWiki\WikibaseExport\Persistence\ConfigJsonValidator;
use ProfessionalWiki\WikibaseExport\Persistence\ConfigLookup;
use ProfessionalWiki\WikibaseExport\Persistence\PageContentFetcher;
use ProfessionalWiki\WikibaseExport\Persistence\WikiConfigLookup;
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

	public function getConfigLookup(): ConfigLookup {
		$deserializer = new ConfigDeserializer(
			ConfigJsonValidator::newInstance()
		);

		return new CombiningConfigLookup(
			baseConfig: (string)MediaWikiServices::getInstance()->getMainConfig()->get( 'WikibaseExport' ),
			deserializer: $deserializer,
			wikiConfigLookup: new WikiConfigLookup(
				contentFetcher: new PageContentFetcher(
					MediaWikiServices::getInstance()->getTitleParser(),
					MediaWikiServices::getInstance()->getRevisionLookup()
				),
				deserializer: $deserializer,
				pageName: self::CONFIG_PAGE_TITLE
			),
			enableWikiRules: (bool)MediaWikiServices::getInstance()->getMainConfig()->get( 'WikibaseExportEnableInWikiConfig' )
		);
	}

}
