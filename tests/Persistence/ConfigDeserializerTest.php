<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Persistence;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\WikibaseExportExtension;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Persistence\ConfigDeserializer
 */
class ConfigDeserializerTest extends TestCase {

	public function testValidJsonReturnsConfig(): void {
		$deserializer = WikibaseExportExtension::getInstance()->newConfigDeserializer();

		$config = $deserializer->deserialize( $this->createValidConfig() );

		$this->assertSame(
			'en',
			$config->entityLabelLanguage
		);
		$this->assertSame(
			'choose foo',
			$config->chooseSubjectsLabel
		);
		$this->assertSame(
			'filter foo',
			$config->filterSubjectsLabel
		);
		$this->assertSame(
			[ 'Q1', 'Q2' ],
			$config->defaultSubjects
		);
		$this->assertSame(
			2010,
			$config->defaultStartYear
		);
		$this->assertSame(
			2022,
			$config->defaultEndYear
		);
		$this->assertSame(
			'P1',
			$config->startYearPropertyId
		);
		$this->assertSame(
			'P2',
			$config->endYearPropertyId
		);
		$this->assertSame(
			'P3',
			$config->pointInTimePropertyId
		);
		$this->assertSame(
			[ 'P4', 'P5' ],
			$config->properties
		);
		$this->assertSame(
			'Lorem ipsum',
			$config->introText
		);
	}

	public function testInvalidJsonReturnsEmptyConfig(): void {
		// TODO: implement validator
	}

	private function createValidConfig(): string {
		return '
{
    "entityLabelLanguage": "en",
    "chooseSubjectsLabel": "choose foo",
    "filterSubjectsLabel": "filter foo",
    "defaultSubjects": [
        "Q1",
        "Q2"
    ],
    "defaultStartYear": 2010,
    "defaultEndYear": 2022,
    "startYearPropertyId": "P1",
    "endYearPropertyId": "P2",
    "pointInTimePropertyId": "P3",
    "properties": [
        "P4",
        "P5"
    ],
    "introText": "Lorem ipsum"
}
';
	}

}
