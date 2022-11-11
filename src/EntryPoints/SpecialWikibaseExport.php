<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\EntryPoints;

use MediaWiki\MediaWikiServices;
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
		// TODO: create extension config retrieval service
		$properties = MediaWikiServices::getInstance()->getMainConfig()->get( 'WikibaseExportProperties' );
		return [
			'wgWikibaseExportProperties' => $properties,
		];
	}

}
