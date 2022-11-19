<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\EntryPoints;

use ProfessionalWiki\WikibaseExport\Application\Config;
use ProfessionalWiki\WikibaseExport\WikibaseExportExtension;
use SpecialPage;

class SpecialWikibaseExport extends SpecialPage {

	public function __construct() {
		parent::__construct( 'WikibaseExport' );
	}

	public function execute( $subPage ): void {
		parent::execute( $subPage );
		$output = $this->getOutput();
		$output->enableOOUI();
		$output->addModules( 'ext.wikibase.export' );
		$output->addHTML( '<div id="wikibase-export"></div>' );
		$output->addJsConfigVars( $this->getJsConfigVars() );
	}

	public function getGroupName(): string {
		return 'wikibase';
	}

	public function getDescription(): string {
		return $this->msg( 'special-wikibase-export' )->escaped();
	}

	/**
	 * @return array<string, mixed>
	 */
	private function getJsConfigVars(): array {
		$config = WikibaseExportExtension::getInstance()->newConfigLookup()->getConfig();

		return [
			'wgWikibaseExport' => $this->configToVars( $config )
		];
	}

	/**
	 * @return array<string, mixed>
	 */
	private function configToVars( Config $config ): array {
		return [
			'entityLabelLanguage' => $config->entityLabelLanguage,
			'chooseSubjectsLabel' => $config->chooseSubjectsLabel,
			'filterSubjectsLabel' => $config->filterSubjectsLabel,
			'defaultSubjects' => $config->defaultSubjects,
			'defaultStartYear' => $config->defaultStartYear,
			'defaultEndYear' => $config->defaultEndYear,
			'properties' => $config->properties
		];
	}

}
