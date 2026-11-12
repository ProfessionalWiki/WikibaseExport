<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Persistence;

use MediaWiki\Content\Content;
use MediaWiki\Page\PageLookup;
use MediaWiki\Revision\RevisionLookup;

class PageContentFetcher {

	public function __construct(
		private PageLookup $pageLookup,
		private RevisionLookup $revisionLookup
	) {
	}

	public function getPageContent( string $pageTitle ): ?Content {
		$page = $this->pageLookup->getPageByText( $pageTitle );

		if ( $page === null ) {
			return null;
		}

		$revision = $this->revisionLookup->getRevisionByTitle( $page );

		return $revision?->getContent( 'main' );
	}

}
