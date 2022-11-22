<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application;

use RuntimeException;

class Config {

	/**
	 * @param string[]|null $defaultSubjects
	 * @param string[]|null $properties
	 */
	public function __construct(
		public /* readonly */ ?string $entityLabelLanguage = null,
		public /* readonly */ ?string $chooseSubjectsLabel = null,
		public /* readonly */ ?string $filterSubjectsLabel = null,
		public /* readonly */ ?array $defaultSubjects = null,
		public /* readonly */ ?int $defaultStartYear = null,
		public /* readonly */ ?int $defaultEndYear = null,
		public /* readonly */ ?string $startTimePropertyId = null,
		public /* readonly */ ?string $endTimePropertyId = null,
		public /* readonly */ ?string $pointInTimePropertyId = null,
		public /* readonly */ ?array $properties = null,
		public /* readonly */ ?string $introText = null
	) {
	}

	public function combine( Config $config ): self {
		return new Config(
			$config->entityLabelLanguage ?? $this->entityLabelLanguage,
			$config->chooseSubjectsLabel ?? $this->chooseSubjectsLabel,
			$config->filterSubjectsLabel ?? $this->filterSubjectsLabel,
			$config->defaultSubjects ?? $this->defaultSubjects,
			$config->defaultStartYear ?? $this->defaultStartYear,
			$config->defaultEndYear ?? $this->defaultEndYear,
			$config->startTimePropertyId ?? $this->startTimePropertyId,
			$config->endTimePropertyId ?? $this->endTimePropertyId,
			$config->pointInTimePropertyId ?? $this->pointInTimePropertyId,
			$config->properties ?? $this->properties,
			$config->introText ?? $this->introText,
		);
	}

	public function getStartTimePropertyId(): string {
		if ( $this->startTimePropertyId === null ) {
			throw new RuntimeException( 'Config is incomplete' );
		}

		return $this->startTimePropertyId;
	}

	public function getEndTimePropertyId(): string {
		if ( $this->endTimePropertyId === null ) {
			throw new RuntimeException( 'Config is incomplete' );
		}

		return $this->endTimePropertyId;
	}

	public function getPointInTimePropertyId(): string {
		if ( $this->pointInTimePropertyId === null ) {
			throw new RuntimeException( 'Config is incomplete' );
		}

		return $this->pointInTimePropertyId;
	}

}
