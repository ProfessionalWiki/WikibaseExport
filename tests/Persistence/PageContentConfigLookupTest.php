<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Persistence;

use ProfessionalWiki\WikibaseExport\Application\Config;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\Valid;
use ProfessionalWiki\WikibaseExport\Tests\WikibaseExportIntegrationTest;
use ProfessionalWiki\WikibaseExport\WikibaseExportExtension;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Persistence\PageContentConfigLookup
 * @group Database
 */
class PageContentConfigLookupTest extends WikibaseExportIntegrationTest {

	public function testEmptyPageConfig(): void {
		$this->editConfigPage( '{}' );
		$lookup = WikibaseExportExtension::getInstance()->newPageContentConfigLookup();

		$config = $lookup->getConfig();
		$emptyConfig = new Config();

		$this->assertEquals( $emptyConfig, $config );
	}

	public function testSavedPageConfig(): void {
		$this->editConfigPage( Valid::configJson() );
		$lookup = WikibaseExportExtension::getInstance()->newPageContentConfigLookup();

		$config = $lookup->getConfig();

		$this->assertEquals(
			new Config(
				defaultSubjects: [ 'Q1', 'Q2' ],
				defaultStartYear: 2010,
				defaultEndYear: 2022,
				startTimePropertyId: 'P1',
				endTimePropertyId: 'P2',
				pointInTimePropertyId: 'P3',
				propertiesToGroupByYear: [ 'P4', 'P5' ],
				propertiesWithoutQualifiers: [ 'P6', 'P7' ],
				subjectFilterPropertyId: 'P10',
				subjectFilterPropertyValue: 'company'
			),
			$config
		);
	}

}
