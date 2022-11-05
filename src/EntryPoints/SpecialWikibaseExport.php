<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\EntryPoints;

use SpecialPage;

class SpecialWikibaseExport extends SpecialPage {

	public function __construct() {
		parent::__construct( 'WikibaseExport' );
	}

	public function execute( $subPage ): void {
		parent::execute( $subPage );
		$this->getOutput()->addHTML( 'TODO' );
	}

	public function getGroupName(): string {
		return 'wikibase';
	}

	public function getDescription(): string {
		return $this->msg( 'special-wikibase-export' )->escaped();
	}

}
