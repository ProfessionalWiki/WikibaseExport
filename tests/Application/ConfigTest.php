<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\Config;
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
			startTimePropertyId: 'P1',
			endTimePropertyId: 'P2',
			pointInTimePropertyId: 'P3',
			propertiesGroupedByYear: [ new NumericPropertyId( 'P10' ), new NumericPropertyId( 'P11' ) ],
			ungroupedProperties: [ new NumericPropertyId( 'P12' ), new NumericPropertyId( 'P13' ) ],
			subjectFilterPropertyId: 'P15',
			subjectFilterPropertyValue: 'company'
		);
	}

	private function createNewConfig(): Config {
		return new Config(
			defaultSubjects: [ 'Q3', 'Q4' ],
			defaultStartYear: 1990,
			defaultEndYear: 2000,
			startTimePropertyId: 'P4',
			endTimePropertyId: 'P5',
			pointInTimePropertyId: 'P6',
			propertiesGroupedByYear: [ new NumericPropertyId( 'P20' ), new NumericPropertyId( 'P21' ) ],
			ungroupedProperties: [ new NumericPropertyId( 'P22' ), new NumericPropertyId( 'P23' ) ],
			subjectFilterPropertyId: 'P25',
			subjectFilterPropertyValue: 'organization'
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

	public function testGetAllProperties(): void {
		$original = $this->createOriginalConfig();

		$this->assertEquals(
			[
				new NumericPropertyId( 'P10' ),
				new NumericPropertyId( 'P11' ),
				new NumericPropertyId( 'P12' ),
				new NumericPropertyId( 'P13' ),
			],
			$original->getAllProperties()
		);
	}

}
