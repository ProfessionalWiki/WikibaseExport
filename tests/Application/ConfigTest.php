<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\Config;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\Config
 */
class ConfigTest extends TestCase {

	private function createOriginalConfig(): Config {
		return new Config(
			entityLabelLanguage: null,
			chooseSubjectsLabel: 'choose foo',
			filterSubjectsLabel: 'filter foo',
			defaultSubjects: [ 'Q1', 'Q2' ],
			defaultStartYear: 2000,
			defaultEndYear: 2022,
			startTimePropertyId: 'P1',
			endTimePropertyId: 'P2',
			pointInTimePropertyId: 'P3',
			properties: [ 'P10', 'P11' ],
			introText: 'foo bar'
		);
	}

	private function createNewConfig(): Config {
		return new Config(
			entityLabelLanguage: 'de',
			chooseSubjectsLabel: 'choose bar',
			filterSubjectsLabel: 'filter bar',
			defaultSubjects: [ 'Q3', 'Q4' ],
			defaultStartYear: 1990,
			defaultEndYear: 2000,
			startTimePropertyId: 'P4',
			endTimePropertyId: 'P5',
			pointInTimePropertyId: 'P6',
			properties: [ 'P20', 'P21' ],
			introText: 'lorem ipsum'
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
