<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Persistence;

use ProfessionalWiki\WikibaseExport\Application\Config;

interface ConfigLookup {

	public function getConfig(): Config;

}
