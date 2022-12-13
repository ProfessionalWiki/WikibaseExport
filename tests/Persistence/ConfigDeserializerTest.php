<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Persistence;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\Config;
use ProfessionalWiki\WikibaseExport\Application\PropertyIdList;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\Valid;
use ProfessionalWiki\WikibaseExport\WikibaseExportExtension;
use Wikibase\DataModel\Entity\NumericPropertyId;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Persistence\ConfigDeserializer
 */
class ConfigDeserializerTest extends TestCase {

	public function testValidJsonReturnsConfig(): void {
		$deserializer = WikibaseExportExtension::getInstance()->newConfigDeserializer();

		$config = $deserializer->deserialize( Valid::configJson() );

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

	public function testInvalidJsonReturnsEmptyConfig(): void {
		$deserializer = WikibaseExportExtension::getInstance()->newConfigDeserializer();

		$config = $deserializer->deserialize( '}{' );
		$emptyConfig = new Config();

		$this->assertEquals( $emptyConfig, $config );
	}

	public function testNullPropertyIdReturnsNull(): void {
		$deserializer = WikibaseExportExtension::getInstance()->newConfigDeserializer();

		$config = $deserializer->deserialize( '{ "startTimePropertyId": null }' );
		$emptyConfig = new Config();

		$this->assertEquals( $emptyConfig, $config );
	}

	public function testInvalidPropertyIdReturnsNull(): void {
		$deserializer = WikibaseExportExtension::getInstance()->newConfigDeserializer();

		$config = $deserializer->deserialize( '{ "startTimePropertyId": "Q1" }' );
		$emptyConfig = new Config();

		$this->assertEquals( $emptyConfig, $config );
	}

}
