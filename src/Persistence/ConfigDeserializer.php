<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Persistence;

use ProfessionalWiki\WikibaseExport\Application\Config;
use ProfessionalWiki\WikibaseExport\Application\PropertyIdList;
use ProfessionalWiki\WikibaseExport\Application\PropertyIdListParser;
use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikibase\DataModel\Entity\PropertyId;

class ConfigDeserializer {

	public function __construct(
		private ConfigJsonValidator $validator,
		private PropertyIdListParser $idListParser
	) {
	}

	public function deserialize( string $configJson ): Config {
		if ( $this->validator->validate( $configJson ) ) {
			$configArray = json_decode( $configJson, true );

			if ( is_array( $configArray ) ) {
				return $this->newConfig( $configArray );
			}
		}

		return new Config();
	}

	/**
	 * @param array<string, mixed> $configArray
	 */
	private function newConfig( array $configArray ): Config {
		return new Config(
			defaultSubjects: $configArray['defaultSubjects'] ?? null,
			defaultStartYear: $configArray['defaultStartYear'] ?? null,
			defaultEndYear: $configArray['defaultEndYear'] ?? null,
			startTimePropertyId: $this->propertyIdOrNull( $configArray, 'startTimePropertyId' ),
			endTimePropertyId: $this->propertyIdOrNull( $configArray, 'endTimePropertyId' ),
			pointInTimePropertyId: $this->propertyIdOrNull( $configArray, 'pointInTimePropertyId' ),
			propertiesGroupedByYear: $this->getPropertiesToGroupByYear( $configArray ),
			ungroupedProperties: $this->getUngroupedProperties( $configArray ),
			subjectFilterPropertyId: $configArray['subjectFilterPropertyId'] ?? null,
			subjectFilterPropertyValue: $configArray['subjectFilterPropertyValue'] ?? null,
			exportLanguages: $configArray['exportLanguages'] ?? null
		);
	}

	private function propertyIdOrNull( array $configArray, string $configKey ): ?PropertyId {
		if ( array_key_exists( $configKey, $configArray ) && $configArray[$configKey] !== null ) {
			// The ConfigJsonValidator already verified the ID format is valid, so we do not need to catch any exceptions.
			return new NumericPropertyId( $configArray[$configKey] );
		}

		return null;
	}

	/**
	 * @param array<string, mixed> $configArray
	 * @return ?PropertyIdList
	 */
	private function getPropertiesToGroupByYear( array $configArray ): ?PropertyIdList {
		if ( array_key_exists( 'propertiesToGroupByYear', $configArray ) ) {
			return $this->idListParser->parse( $configArray['propertiesToGroupByYear'] );
		}

		return null;
	}

	/**
	 * @param array<string, mixed> $configArray
	 * @return ?PropertyIdList
	 */
	private function getUngroupedProperties( array $configArray ): ?PropertyIdList {
		if ( array_key_exists( 'ungroupedProperties', $configArray ) ) {
			return $this->idListParser->parse( $configArray['ungroupedProperties'] );
		}

		return null;
	}

}
