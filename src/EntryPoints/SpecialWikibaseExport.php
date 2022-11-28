<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\EntryPoints;

use Html;
use ProfessionalWiki\WikibaseExport\Application\Config;
use ProfessionalWiki\WikibaseExport\WikibaseExportExtension;
use SpecialPage;

class SpecialWikibaseExport extends SpecialPage {

	public function __construct() {
		parent::__construct(
			'WikibaseExport',
			restriction: 'read'
		);
	}

	public function execute( $subPage ): void {
		parent::execute( $subPage );
		$output = $this->getOutput();
		$output->enableOOUI();
		$output->addModuleStyles( 'ext.wikibase.export.styles' );
		$output->addHTML( '<div id="wikibase-export" class="container">' );

		if ( $this->configIsComplete() ) {
			$output->addModules( 'ext.wikibase.export' );
			$output->addHTML( $this->getIntroText() );
			$output->addJsConfigVars( $this->getJsConfigVars() );
		} else {
			$output->addHTML( $this->getConfigIncompleteWarning() );
		}

		$output->addHTML( '</div>' );
	}

	public function getGroupName(): string {
		return 'wikibase';
	}

	public function getDescription(): string {
		return $this->msg( 'special-wikibase-export' )->escaped();
	}

	private function configIsComplete(): bool {
		return WikibaseExportExtension::getInstance()->newConfigLookup()->getConfig()->isComplete();
	}

	private function getConfigIncompleteWarning(): string {
		return Html::errorBox( $this->msg( 'wikibase-export-config-incomplete' )->parse() );
	}

	private function getIntroText(): string {
		$output = $this->getOutput();

		return $output->msg(
			'wikibase-export-intro',
			$output->msg( 'wikibase-export-download' )->text()
		)->text();
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
			'defaultSubjects' => $config->defaultSubjects,
			'defaultStartYear' => $config->defaultStartYear,
			'defaultEndYear' => $config->defaultEndYear,
			'properties' => $config->properties
		];
	}

}
