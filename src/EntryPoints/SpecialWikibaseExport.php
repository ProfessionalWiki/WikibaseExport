<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\EntryPoints;

use Html;
use MediaWiki\MediaWikiServices;
use ProfessionalWiki\WikibaseExport\Application\Config;
use ProfessionalWiki\WikibaseExport\WikibaseExportExtension;
use SpecialPage;
use Wikibase\DataModel\Entity\PropertyId;

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

		$output->addModules( 'ext.wikibase.export' );
		$output->addHTML( $this->getIntroText() );
		$output->addJsConfigVars( $this->getJsConfigVars() );

		$output->addHTML( '</div>' );
	}

	public function getGroupName(): string {
		return 'wikibase';
	}

	public function getDescription(): string {
		return $this->msg( 'special-wikibase-export' )->escaped();
	}

	private function shouldShowConfigLink(): bool {
		return MediaWikiServices::getInstance()->getMainConfig()->get( 'WikibaseExportEnableInWikiConfig' ) === true
			&& $this->getUser()->isAllowed( 'editinterface' );
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
		$config = WikibaseExportExtension::getInstance()->getConfig();

		return [
			'wgWikibaseExport' => $this->configToVars( $config )
		];
	}

	/**
	 * @return array<string, mixed>
	 */
	private function configToVars( Config $config ): array {
		return [
			'showPropertiesGroupedByYear' => $this->shouldShowPropertiesGroupedByYear( $config ),
			'defaultSubjects' => $config->defaultSubjects,
			'defaultStartYear' => $config->defaultStartYear,
			'defaultEndYear' => $config->defaultEndYear,
			'groupedProperties' => array_map(
				fn( PropertyId $id ) => $id->getSerialization(),
				$config->getPropertiesGroupedByYear()->ids
			),
			'showUngroupedProperties' => $this->shouldShowUngroupedProperties( $config ),
			'ungroupedProperties' => array_map(
				fn( PropertyId $id ) => $id->getSerialization(),
				$config->getUngroupedProperties()->ids
			),
			'showConfigLink' => $this->shouldShowConfigLink()
		];
	}

	private function shouldShowPropertiesGroupedByYear( Config $config ): bool {
		return !$config->getPropertiesGroupedByYear()->isEmpty()
			&& ( $this->pointInTimeIsConfigured( $config ) || $this->timeRangeIsConfigured( $config ) );
	}

	private function pointInTimeIsConfigured( Config $config ): bool {
		return $config->pointInTimePropertyId !== null;
	}

	private function timeRangeIsConfigured( Config $config ): bool {
		return $config->startTimePropertyId !== null
			&& $config->endTimePropertyId !== null;
	}

	private function shouldShowUngroupedProperties( Config $config ): bool {
		return !$config->getUngroupedProperties()->isEmpty();
	}

}
