<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\Config;
use ProfessionalWiki\WikibaseExport\Application\PropertyIdList;
use Wikibase\DataModel\Entity\NumericPropertyId;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\Config
 */
class ConfigTest extends TestCase {

	private function createOriginalConfig(): Config {
		return new Config(
			defaultSubjects: [ 'Q1', 'Q2' ],
			defaultStartYear: 2000,
			defaultEndYear: 2022,
			startTimePropertyId: new NumericPropertyId( 'P1' ),
			endTimePropertyId: new NumericPropertyId( 'P2' ),
			pointInTimePropertyId: new NumericPropertyId( 'P3' ),
			propertiesGroupedByYear: new PropertyIdList( [
				new NumericPropertyId( 'P10' ),
				new NumericPropertyId( 'P11' )
			] ),
			ungroupedProperties: new PropertyIdList( [
				new NumericPropertyId( 'P12' ),
				new NumericPropertyId( 'P13' )
			] ),
			subjectFilterPropertyId: 'P15',
			subjectFilterPropertyValue: 'company',
			exportLanguages: null
		);
	}

	private function createNewConfig(): Config {
		return new Config(
			defaultSubjects: [ 'Q3', 'Q4' ],
			defaultStartYear: 1990,
			defaultEndYear: 2000,
			startTimePropertyId: new NumericPropertyId( 'P4' ),
			endTimePropertyId: new NumericPropertyId( 'P5' ),
			pointInTimePropertyId: new NumericPropertyId( 'P6' ),
			propertiesGroupedByYear: new PropertyIdList( [
				new NumericPropertyId( 'P20' ),
				new NumericPropertyId( 'P21' )
			] ),
			ungroupedProperties: new PropertyIdList( [
				new NumericPropertyId( 'P22' ),
				new NumericPropertyId( 'P23' )
			] ),
			subjectFilterPropertyId: 'P25',
			subjectFilterPropertyValue: 'organization',
			exportLanguages: [ 'en', 'nl' ]
		);
	}

	public function testOriginalValuesAreKeptWhenCombined(): void {
		$original = $this->createOriginalConfig();
		$new = new Config();

		$combined = $original->combine( $new );

		$this->assertEquals( $original, $combined );
	}

	public function testOriginalValuesAreReplacedWhenCombined(): void {
		$original = $this->createOriginalConfig();
		$new = $this->createNewConfig();

		$combined = $original->combine( $new );

		$this->assertEquals( $new, $combined );
	}

}
