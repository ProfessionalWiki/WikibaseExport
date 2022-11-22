<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Persistence;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\Config;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\Valid;
use ProfessionalWiki\WikibaseExport\WikibaseExportExtension;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Persistence\ConfigDeserializer
 */
class ConfigDeserializerTest extends TestCase {

	public function testValidJsonReturnsConfig(): void {
		$deserializer = WikibaseExportExtension::getInstance()->newConfigDeserializer();

		$config = $deserializer->deserialize( Valid::configJson() );

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

	public function testInvalidJsonReturnsEmptyConfig(): void {
		$deserializer = WikibaseExportExtension::getInstance()->newConfigDeserializer();

		$config = $deserializer->deserialize( '}{' );
		$emptyConfig = new Config();

		$this->assertEquals( $emptyConfig, $config );
	}

}
