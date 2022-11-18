<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\EntryPoints;

use SpecialPage;
use Title;

class SpecialWikibaseExportConfig extends SpecialPage {

	public function __construct() {
		parent::__construct( 'WikibaseExportConfig' );
	}

	public function execute( $subPage ): void {
		parent::execute( $subPage );

		$title = Title::newFromText( 'MediaWiki:WikibaseExport' );

		if ( $title instanceof Title ) {
			$this->getOutput()->redirect( $title->getFullURL() );
		}
	}

	public function getGroupName(): string {
		return 'wikibase';
	}

	public function getDescription(): string {
		return $this->msg( 'special-wikibase-export-config' )->escaped();
	}

}
