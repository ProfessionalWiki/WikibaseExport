<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Persistence;

use ProfessionalWiki\WikibaseExport\Application\Config;

class ConfigDeserializer {

	public function __construct(
		private ConfigJsonValidator $validator
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
			$configArray['entityLabelLanguage'] ?? null,
			$configArray['chooseSubjectsLabel'] ?? null,
			$configArray['filterSubjectsLabel'] ?? null,
			$configArray['defaultSubjects'] ?? null,
			$configArray['defaultStartYear'] ?? null,
			$configArray['defaultEndYear'] ?? null,
			$configArray['startTimePropertyId'] ?? null,
			$configArray['endTimePropertyId'] ?? null,
			$configArray['pointInTimePropertyId'] ?? null,
			$configArray['properties'] ?? null,
			$configArray['introText'] ?? null,
		);
	}

}
