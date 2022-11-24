<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Persistence;

use JsonContent;
use ProfessionalWiki\WikibaseExport\Application\Config;

class PageContentConfigLookup implements ConfigLookup {

	public function __construct(
		private PageContentFetcher $contentFetcher,
		private ConfigDeserializer $deserializer,
		private string $pageName
	) {
	}

	public function getConfig(): Config {
		$content = $this->contentFetcher->getPageContent( 'MediaWiki:' . $this->pageName );

		if ( $content instanceof \JsonContent ) {
			return $this->configFromJsonContent( $content );
		}

		return new Config();
	}

	private function configFromJsonContent( JsonContent $content ): Config {
		return $this->deserializer->deserialize( $content->getText() );
	}

}
