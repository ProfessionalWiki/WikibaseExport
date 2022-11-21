<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Persistence;

use ProfessionalWiki\WikibaseExport\Application\Config;
use WMDE\Clock\Clock;

class CombiningConfigLookup implements ConfigLookup {

	public function __construct(
		private string $baseConfig,
		private ConfigDeserializer $deserializer,
		private WikiConfigLookup $wikiConfigLookup,
		private bool $enableWikiRules,
		private Clock $clock
	) {
	}

	public function getConfig(): Config {
		$config = $this->deserializer->deserialize( $this->baseConfig )
			->combine( $this->createDefaultConfig() );

		if ( !$this->enableWikiRules ) {
			return $config;
		}

		return $config->combine( $this->wikiConfigLookup->getConfig() );
	}

	private function createDefaultConfig(): Config {
		return new Config(
			defaultStartYear: $this->getCurrentYear(),
			defaultEndYear: $this->getCurrentYear()
		);
	}

	private function getCurrentYear(): int {
		return (int)( $this->clock->now() )->format( 'Y' );
	}

}
