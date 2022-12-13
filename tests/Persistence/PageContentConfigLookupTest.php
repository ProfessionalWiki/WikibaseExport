<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Persistence;

use ProfessionalWiki\WikibaseExport\Application\Config;
use ProfessionalWiki\WikibaseExport\Application\PropertyIdList;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\Valid;
use ProfessionalWiki\WikibaseExport\Tests\WikibaseExportIntegrationTest;
use ProfessionalWiki\WikibaseExport\WikibaseExportExtension;
use Wikibase\DataModel\Entity\NumericPropertyId;

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
				startTimePropertyId: new NumericPropertyId( 'P1' ),
				endTimePropertyId: new NumericPropertyId( 'P2' ),
				pointInTimePropertyId: new NumericPropertyId( 'P3' ),
				propertiesGroupedByYear: new PropertyIdList( [
					new NumericPropertyId( 'P4' ),
					new NumericPropertyId( 'P5' )
				] ),
				ungroupedProperties: new PropertyIdList( [
					new NumericPropertyId( 'P6' ),
					new NumericPropertyId( 'P7' )
				] ),
				subjectFilterPropertyId: 'P10',
				subjectFilterPropertyValue: 'company',
				exportLanguages: [ 'en', 'nl' ]
			),
			$config
		);
	}

}
