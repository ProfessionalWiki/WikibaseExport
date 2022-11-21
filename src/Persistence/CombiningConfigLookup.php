<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Persistence;

use ProfessionalWiki\WikibaseExport\Application\Config;
use WMDE\Clock\Clock;

/**
 * Combines these config sources, with the latter overriding the former:
 * * Defaults
 * * $baseConfig (LocalSettings.php)
 * * ConfigLookup (MediaWiki:WikibaseExport)
 */
class CombiningConfigLookup implements ConfigLookup {

	public function __construct(
		private string $baseConfig,
		private ConfigDeserializer $deserializer,
		private ConfigLookup $configLookup,
		private bool $enableWikiConfig,
		private Clock $clock
	) {
	}

	public function getConfig(): Config {
		$config = $this->createDefaultConfig()->combine(
			$this->deserializer->deserialize( $this->baseConfig )
		);

		if ( !$this->enableWikiConfig ) {
			return $config;
		}

		return $config->combine( $this->configLookup->getConfig() );
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
