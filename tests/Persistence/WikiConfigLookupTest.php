<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Persistence;

use ProfessionalWiki\WikibaseExport\Application\Config;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\Valid;
use ProfessionalWiki\WikibaseExport\Tests\WikibaseExportIntegrationTest;
use ProfessionalWiki\WikibaseExport\WikibaseExportExtension;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Persistence\WikiConfigLookup
 * @group Database
 */
class WikiConfigLookupTest extends WikibaseExportIntegrationTest {

	public function testEmptyPageConfig(): void {
		$this->editConfigPage( '{}' );
		$lookup = WikibaseExportExtension::getInstance()->newWikiConfigLookup();

		$config = $lookup->getConfig();
		$emptyConfig = new Config();

		$this->assertEquals( $emptyConfig, $config );
	}

	public function testSavedPageConfig(): void {
		$this->editConfigPage( Valid::configJson() );
		$lookup = WikibaseExportExtension::getInstance()->newWikiConfigLookup();

		$config = $lookup->getConfig();

		$this->assertEquals(
			new Config(
				entityLabelLanguage: 'en',
				chooseSubjectsLabel: 'choose foo',
				filterSubjectsLabel: 'filter foo',
				defaultSubjects: [ 'Q1', 'Q2' ],
				defaultStartYear: 2010,
				defaultEndYear: 2022,
				startTimePropertyId: 'P1',
				endTimePropertyId: 'P2',
				pointInTimePropertyId: 'P3',
				properties: [ 'P4', 'P5' ],
				introText: 'Lorem ipsum',
			),
			$config
		);
	}

}
