<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport;

use MediaWiki\MediaWikiServices;
use ProfessionalWiki\WikibaseExport\Application\EntityMapperFactory;
use ProfessionalWiki\WikibaseExport\Application\EntitySourceFactory;
use ProfessionalWiki\WikibaseExport\Application\Export\ExportPresenter;
use ProfessionalWiki\WikibaseExport\Application\Export\ExportUseCase;
use ProfessionalWiki\WikibaseExport\Application\Export\StatementMapper;
use ProfessionalWiki\WikibaseExport\Application\TimeQualifierProperties;
use ProfessionalWiki\WikibaseExport\EntryPoints\ExportApi;
use ProfessionalWiki\WikibaseExport\Persistence\CombiningConfigLookup;
use ProfessionalWiki\WikibaseExport\Persistence\ConfigDeserializer;
use ProfessionalWiki\WikibaseExport\Persistence\ConfigJsonValidator;
use ProfessionalWiki\WikibaseExport\Persistence\ConfigLookup;
use ProfessionalWiki\WikibaseExport\Persistence\PageContentFetcher;
use ProfessionalWiki\WikibaseExport\Persistence\PageContentConfigLookup;
use Title;
use ValueFormatters\FormatterOptions;
use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikibase\Lib\Formatters\SnakFormatter;
use Wikibase\Repo\WikibaseRepo;
use WMDE\Clock\SystemClock;

/**
 * Top level factory for the WikibaseExportExtension extension
 */
class WikibaseExportExtension {

	public const CONFIG_PAGE_TITLE = 'WikibaseExport';

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

	public function newConfigLookup(): ConfigLookup {
		return new CombiningConfigLookup(
			baseConfig: (string)MediaWikiServices::getInstance()->getMainConfig()->get( 'WikibaseExport' ),
			deserializer: $this->newConfigDeserializer(),
			configLookup: $this->newPageContentConfigLookup(),
			enableWikiConfig: (bool)MediaWikiServices::getInstance()->getMainConfig()->get( 'WikibaseExportEnableInWikiConfig' ),
			clock: new SystemClock()
		);
	}

	public function newPageContentConfigLookup(): PageContentConfigLookup {
		return new PageContentConfigLookup(
			contentFetcher: new PageContentFetcher(
				MediaWikiServices::getInstance()->getTitleParser(),
				MediaWikiServices::getInstance()->getRevisionLookup()
			),
			deserializer: $this->newConfigDeserializer(),
			pageName: self::CONFIG_PAGE_TITLE
		);
	}

	public function newConfigDeserializer(): ConfigDeserializer {
		return new ConfigDeserializer(
			ConfigJsonValidator::newInstance()
		);
	}

	public function newTimeQualifierProperties(): TimeQualifierProperties {
		$config = $this->newConfigLookup()->getConfig();

		return new TimeQualifierProperties(
			pointInTime: new NumericPropertyId( $config->getPointInTimePropertyId() ),
			startTime: new NumericPropertyId( $config->getStartTimePropertyId() ),
			endTime: new NumericPropertyId( $config->getEndTimePropertyId() ),
		);
	}

	public function newStatementMapper(): StatementMapper {
		return new StatementMapper(
			snakFormatter: WikibaseRepo::getSnakFormatterFactory()->getSnakFormatter(
				SnakFormatter::FORMAT_PLAIN,
				new FormatterOptions()
			)
		);
	}

	public function newExportUseCase( ExportPresenter $presenter ): ExportUseCase {
		return new ExportUseCase(
			entitySourceFactory: new EntitySourceFactory(
				lookup: WikibaseRepo::getEntityLookup()
			),
			entityMapperFactory: new EntityMapperFactory(
				timeQualifierProperties: $this->newTimeQualifierProperties(),
				statementMapper: $this->newStatementMapper()
			),
			presenter: $presenter
		);
	}

}
