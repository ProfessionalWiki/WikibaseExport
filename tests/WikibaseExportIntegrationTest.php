<?php

namespace ProfessionalWiki\WikibaseExport\Tests;

use MediaWikiIntegrationTestCase;
use ProfessionalWiki\WikibaseExport\WikibaseExportExtension;

class WikibaseExportIntegrationTest extends MediaWikiIntegrationTestCase {

	protected function editConfigPage( string $config ): void {
		$this->editPage(
			'MediaWiki:' . WikibaseExportExtension::CONFIG_PAGE_TITLE,
			$config
		);
	}

}
