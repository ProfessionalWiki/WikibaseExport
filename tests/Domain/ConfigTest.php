<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Domain\Config;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Domain\Config
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
			startYearPropertyId: 'P1',
			endYearPropertyId: 'P2',
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
			startYearPropertyId: 'P4',
			endYearPropertyId: 'P5',
			pointInTimePropertyId: 'P6',
			properties: [ 'P20', 'P21' ],
			introText: 'lorem ipsum'
		);
	}

	public function testOriginalValuesAreKept(): void {
		$original = $this->createOriginalConfig();
		$new = new Config();

		$combined = $original->combine( $new );

		$this->assertSame(
			$original->entityLabelLanguage,
			$combined->entityLabelLanguage
		);
		$this->assertSame(
			$original->chooseSubjectsLabel,
			$combined->chooseSubjectsLabel
		);
		$this->assertSame(
			$original->filterSubjectsLabel,
			$combined->filterSubjectsLabel
		);
		$this->assertSame(
			$original->defaultSubjects,
			$combined->defaultSubjects
		);
		$this->assertSame(
			$original->defaultStartYear,
			$combined->defaultStartYear
		);
		$this->assertSame(
			$original->defaultEndYear,
			$combined->defaultEndYear
		);
		$this->assertSame(
			$original->startYearPropertyId,
			$combined->startYearPropertyId
		);
		$this->assertSame(
			$original->endYearPropertyId,
			$combined->endYearPropertyId
		);
		$this->assertSame(
			$original->pointInTimePropertyId,
			$combined->pointInTimePropertyId
		);
		$this->assertSame(
			$original->properties,
			$combined->properties
		);
		$this->assertSame(
			$original->introText,
			$combined->introText
		);
	}

	public function testOriginalValuesAreReplaced(): void {
		$original = $this->createOriginalConfig();
		$new = $this->createNewConfig();

		$combined = $original->combine( $new );

		$this->assertSame(
			$new->entityLabelLanguage,
			$combined->entityLabelLanguage
		);
		$this->assertSame(
			$new->chooseSubjectsLabel,
			$combined->chooseSubjectsLabel
		);
		$this->assertSame(
			$new->filterSubjectsLabel,
			$combined->filterSubjectsLabel
		);
		$this->assertSame(
			$new->defaultSubjects,
			$combined->defaultSubjects
		);
		$this->assertSame(
			$new->defaultStartYear,
			$combined->defaultStartYear
		);
		$this->assertSame(
			$new->defaultEndYear,
			$combined->defaultEndYear
		);
		$this->assertSame(
			$new->startYearPropertyId,
			$combined->startYearPropertyId
		);
		$this->assertSame(
			$new->endYearPropertyId,
			$combined->endYearPropertyId
		);
		$this->assertSame(
			$new->pointInTimePropertyId,
			$combined->pointInTimePropertyId
		);
		$this->assertSame(
			$new->properties,
			$combined->properties
		);
		$this->assertSame(
			$new->introText,
			$combined->introText
		);
	}

}
