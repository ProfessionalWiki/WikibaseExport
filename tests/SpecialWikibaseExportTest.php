<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests;

use ProfessionalWiki\WikibaseExport\EntryPoints\SpecialWikibaseExport;
use SpecialPageTestBase;

/**
 * @covers \ProfessionalWiki\WikibaseExport\EntryPoints\SpecialWikibaseExport
 */
class SpecialWikibaseExportTest extends SpecialPageTestBase {

	protected function newSpecialPage(): SpecialWikibaseExport {
		return new SpecialWikibaseExport();
	}

	public function testStub(): void {
		/** @var string $output */
		[ $output ] = $this->executeSpecialPage();

		$this->assertStringContainsString(
			'TODO',
			$output
		);
	}

}
