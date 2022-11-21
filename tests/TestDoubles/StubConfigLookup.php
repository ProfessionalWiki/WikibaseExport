<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\TestDoubles;

use ProfessionalWiki\WikibaseExport\Application\Config;
use ProfessionalWiki\WikibaseExport\Persistence\ConfigLookup;

class StubConfigLookup implements ConfigLookup {

	public function __construct(
		private Config $config
	) {
	}

	public function getConfig(): Config {
		return $this->config;
	}

}
