<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Persistence;

use ProfessionalWiki\WikibaseExport\Domain\Config;

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
			$configArray['defaultSubjects'] ?? [],
			$configArray['defaultStartYear'] ?? null,
			$configArray['defaultEndYear'] ?? null,
			$configArray['startYearPropertyId'] ?? null,
			$configArray['endYearPropertyId'] ?? null,
			$configArray['pointInTimePropertyId'] ?? null,
			$configArray['properties'] ?? [],
			$configArray['introText'] ?? '',
		);
	}

}
