<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Persistence;

use ProfessionalWiki\WikibaseExport\Application\Config;

class CombiningConfigLookup implements ConfigLookup {

	public function __construct(
		private string $baseConfig,
		private ConfigDeserializer $deserializer,
		private WikiConfigLookup $wikiConfigLookup,
		private bool $enableWikiRules
	) {
	}

	public function getConfig(): Config {
		$config = $this->deserializer->deserialize( $this->baseConfig );

		if ( !$this->enableWikiRules ) {
			return $config;
		}

		return $config->combine( $this->wikiConfigLookup->getConfig() );
	}

}
