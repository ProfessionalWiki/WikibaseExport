<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Persistence;

use Content;
use MalformedTitleException;
use MediaWiki\Revision\RevisionLookup;
use TitleParser;

class PageContentFetcher {

	public function __construct(
		private TitleParser $titleParser,
		private RevisionLookup $revisionLookup
	) {
	}

	public function getPageContent( string $pageTitle ): ?Content {
		try {
			$title = $this->titleParser->parseTitle( $pageTitle );
		} catch ( MalformedTitleException ) {
			return null;
		}

		$revision = $this->revisionLookup->getRevisionByTitle( $title );

		return $revision?->getContent( 'main' );
	}

}
