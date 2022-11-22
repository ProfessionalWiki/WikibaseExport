<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Persistence;

use DateTimeImmutable;
use ProfessionalWiki\WikibaseExport\Application\Config;
use ProfessionalWiki\WikibaseExport\Persistence\CombiningConfigLookup;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\StubConfigLookup;
use ProfessionalWiki\WikibaseExport\Tests\WikibaseExportIntegrationTest;
use ProfessionalWiki\WikibaseExport\WikibaseExportExtension;
use WMDE\Clock\StubClock;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Persistence\CombiningConfigLookup
 */
class CombiningConfigLookupTest extends WikibaseExportIntegrationTest {

	public function testUsesCurrentYearAsDefault(): void {
		$lookup = $this->newLookup(
			baseConfig: '{}',
			wikiConfig: new Config(),
			enableWikiConfig: true
		);

		$this->assertSame(
			2022,
			$lookup->getConfig()->defaultStartYear
		);

		$this->assertSame(
			2022,
			$lookup->getConfig()->defaultEndYear
		);
	}

	private function newLookup( string $baseConfig, Config $wikiConfig, bool $enableWikiConfig ): CombiningConfigLookup {
		return new CombiningConfigLookup(
			baseConfig: $baseConfig,
			deserializer: WikibaseExportExtension::getInstance()->newConfigDeserializer(),
			configLookup: new StubConfigLookup( $wikiConfig ),
			enableWikiConfig: $enableWikiConfig,
			clock: new StubClock( new DateTimeImmutable( '2022-11-21' ) )
		);
	}

	public function testWikiConfigSupersedesBaseConfig(): void {
		$lookup = $this->newLookup(
			baseConfig: '{ "defaultStartYear": 2000 }',
			wikiConfig: new Config( defaultStartYear: 2042 ),
			enableWikiConfig: true
		);

		$this->assertSame(
			2042,
			$lookup->getConfig()->defaultStartYear
		);
	}

	public function testUsesBaseConfigIfThereIsNoWikiConfig(): void {
		$lookup = $this->newLookup(
			baseConfig: '{ "defaultStartYear": 2000, "defaultEndYear": 31337 }',
			wikiConfig: new Config( defaultStartYear: 2042 ),
			enableWikiConfig: true
		);

		$this->assertSame(
			31337,
			$lookup->getConfig()->defaultEndYear
		);
	}

	public function testOnlyUsesWikiConfigWhenEnabled(): void {
		$lookup = $this->newLookup(
			baseConfig: '{ "defaultStartYear": 2000 }',
			wikiConfig: new Config( defaultStartYear: 2042 ),
			enableWikiConfig: false
		);

		$this->assertSame(
			2000,
			$lookup->getConfig()->defaultStartYear
		);
	}

	public function testCombinedConfigHasInvalidDateRange(): void {
		$lookup = $this->newLookup(
			baseConfig: '{ "defaultStartYear": 2020 }',
			wikiConfig: new Config( defaultEndYear: 1010 ),
			enableWikiConfig: true
		);

		$this->assertSame(
			2020,
			$lookup->getConfig()->defaultStartYear
		);

		$this->assertSame(
			1010,
			$lookup->getConfig()->defaultEndYear
		);
	}

}
