<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Presentation;

use ProfessionalWiki\WikibaseExport\Tests\WikibaseExportIntegrationTest;

class ConfigPageSmokeTest extends WikibaseExportIntegrationTest {

	public function testSmoke(): void {
		$this->assertStringContainsString(
			'defaultSubjects',
			$this->getPageHtml( 'MediaWiki:WikibaseExport' )
		);
	}

	// TODO: smoke test for the edit page

}
