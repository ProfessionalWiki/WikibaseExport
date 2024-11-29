<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Presentation;

use ProfessionalWiki\WikibaseExport\Tests\WikibaseExportIntegrationTest;

/**
 * @group Database
 */
class ConfigPageSmokeTest extends WikibaseExportIntegrationTest {

	public function testSmoke(): void {
		$this->assertStringContainsString(
			'defaultSubjects',
			$this->getPageHtml( 'MediaWiki:WikibaseExport' )
		);
	}

	public function testEditSmoke(): void {
		$html = $this->getEditPageHtml( 'MediaWiki:WikibaseExport' );

		// Default value
		$this->assertStringContainsString(
			'"startTimePropertyId": "P1"',
			$html
		);

		// Intro text
		$this->assertStringContainsString(
			'view the configuration documentation',
			$html
		);

		// Documentation section
		$this->assertStringContainsString(
			'Configuration documentation',
			$html
		);
	}

}
