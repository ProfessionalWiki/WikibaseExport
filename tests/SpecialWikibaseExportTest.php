<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests;

use ProfessionalWiki\WikibaseExport\SpecialWikibaseExport;
use SpecialPageTestBase;

/**
 * @covers \ProfessionalWiki\WikibaseExport\SpecialWikibaseExport
 */
class SpecialWikibaseExportTest extends SpecialPageTestBase {

	protected function newSpecialPage(): SpecialWikibaseExport {
		return new SpecialWikibaseExport();
	}

	public function testStub(): void {
		/** @var string $output */
		list( $output ) = $this->executeSpecialPage();

		$this->assertStringContainsString(
			'TODO',
			$output
		);
	}

}
