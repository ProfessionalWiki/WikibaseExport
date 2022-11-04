<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\WikibaseExportExtension;

/**
 * @covers \ProfessionalWiki\WikibaseExport\WikibaseExportExtension
 */
class StubTest extends TestCase {

	public function testStub(): void {
		$this->assertNotNull(
			WikibaseExportExtension::getInstance()
		);
	}

}
