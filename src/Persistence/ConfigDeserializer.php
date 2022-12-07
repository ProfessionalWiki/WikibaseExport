<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Persistence;

use ProfessionalWiki\WikibaseExport\Application\Config;
use ProfessionalWiki\WikibaseExport\Application\PropertyIdListParser;
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
			$configArray['defaultSubjects'] ?? null,
			$configArray['defaultStartYear'] ?? null,
			$configArray['defaultEndYear'] ?? null,
			$configArray['startTimePropertyId'] ?? null,
			$configArray['endTimePropertyId'] ?? null,
			$configArray['pointInTimePropertyId'] ?? null,
			$this->getPropertiesToGroupByYear( $configArray ),
			$this->getUngroupedProperties( $configArray ),
			$configArray['subjectFilterPropertyId'] ?? null,
			$configArray['subjectFilterPropertyValue'] ?? null
		);
	}

	/**
	 * @param array<string, mixed> $configArray
	 * @return ?PropertyId[]
	 */
	private function getPropertiesToGroupByYear( array $configArray ): ?array {
		if ( array_key_exists( 'propertiesToGroupByYear', $configArray ) ) {
			return $this->idListParser->parse( $configArray['propertiesToGroupByYear'] );
		}

		return null;
	}

	/**
	 * @param array<string, mixed> $configArray
	 * @return ?PropertyId[]
	 */
	private function getUngroupedProperties( array $configArray ): ?array {
		if ( array_key_exists( 'ungroupedProperties', $configArray ) ) {
			return $this->idListParser->parse( $configArray['ungroupedProperties'] );
		}

		return null;
	}

}
