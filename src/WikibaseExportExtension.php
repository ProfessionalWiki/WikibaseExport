<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport;

use MediaWiki\MediaWikiServices;
use MediaWiki\Permissions\Authority;
use ProfessionalWiki\WikibaseExport\Application\Config;
use ProfessionalWiki\WikibaseExport\Application\EntitySourceFactory;
use ProfessionalWiki\WikibaseExport\Application\Export\ExportPresenter;
use ProfessionalWiki\WikibaseExport\Application\Export\ExportUseCase;
use ProfessionalWiki\WikibaseExport\Application\Export\ProductionValueSetCreatorFactory;
use ProfessionalWiki\WikibaseExport\Application\PropertyIdListParser;
use ProfessionalWiki\WikibaseExport\Application\SearchEntities\SearchEntitiesPresenter;
use ProfessionalWiki\WikibaseExport\Application\SearchEntities\SearchEntitiesUseCase;
use ProfessionalWiki\WikibaseExport\Application\TimeQualifierProperties;
use ProfessionalWiki\WikibaseExport\EntryPoints\ExportApi;
use ProfessionalWiki\WikibaseExport\EntryPoints\SearchEntitiesApi;
use ProfessionalWiki\WikibaseExport\Persistence\AuthorityBasedExportAuthorizer;
use ProfessionalWiki\WikibaseExport\Persistence\CombiningConfigLookup;
use ProfessionalWiki\WikibaseExport\Persistence\ConfigDeserializer;
use ProfessionalWiki\WikibaseExport\Persistence\ConfigJsonValidator;
use ProfessionalWiki\WikibaseExport\Persistence\ConfigLookup;
use ProfessionalWiki\WikibaseExport\Persistence\PageContentConfigLookup;
use ProfessionalWiki\WikibaseExport\Persistence\PageContentFetcher;
use Title;
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

	private ?Config $config;

	private function newExportApi(): ExportApi {
		return new ExportApi();
	}

	public function isConfigTitle( Title $title ): bool {
		return $title->getNamespace() === NS_MEDIAWIKI
			&& $title->getText() === self::CONFIG_PAGE_TITLE;
	}

	public function getConfig(): Config {
		 $this->config ??= $this->newConfigLookup()->getConfig();
		 return $this->config;
	}

	public function clearConfig(): void {
		$this->config = null;
	}

	private function newConfigLookup(): ConfigLookup {
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
			ConfigJsonValidator::newInstance(),
			new PropertyIdListParser()
		);
	}

	public function newTimeQualifierProperties(): TimeQualifierProperties {
		$config = $this->getConfig();

		return new TimeQualifierProperties(
			pointInTime: $config->pointInTimePropertyId,
			startTime: $config->startTimePropertyId,
			endTime: $config->endTimePropertyId
		);
	}

	public function newExportUseCase( ExportPresenter $presenter, Authority $authority ): ExportUseCase {
		return new ExportUseCase(
			ungroupedProperties: $this->getConfig()->getUngroupedProperties(),
			propertiesGroupedByYear: $this->getConfig()->getPropertiesGroupedByYear(),
			timeQualifierProperties: $this->newTimeQualifierProperties(),
			entitySourceFactory: new EntitySourceFactory(
				lookup: WikibaseRepo::getEntityLookup()
			),
			presenter: $presenter,
			authorizer: new AuthorityBasedExportAuthorizer(
				authority: $authority
			),
			valueSetCreatorFactory: new ProductionValueSetCreatorFactory(),
			termLookup: WikibaseRepo::getTermLookup(),
		);
	}

	public static function searchEntitiesApiFactory(): SearchEntitiesApi {
		return self::getInstance()->newSearchEntitiesApi();
	}

	private function newSearchEntitiesApi(): SearchEntitiesApi {
		return new SearchEntitiesApi();
	}

	public function newSearchEntitiesUseCase( SearchEntitiesPresenter $presenter ): SearchEntitiesUseCase {
		return new SearchEntitiesUseCase(
			subjectFilterPropertyId: $this->getConfig()->subjectFilterPropertyId,
			subjectFilterPropertyValue: $this->getConfig()->subjectFilterPropertyValue,
			entitySearchHelper: WikibaseRepo::getEntitySearchHelper(),
			entityLookup: WikibaseRepo::getEntityLookup(),
			presenter: $presenter
		);
	}

}
