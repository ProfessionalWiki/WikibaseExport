<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application;

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
		public /* readonly */ ?string $startYearPropertyId = null,
		public /* readonly */ ?string $endYearPropertyId = null,
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
			$config->startYearPropertyId ?? $this->startYearPropertyId,
			$config->endYearPropertyId ?? $this->endYearPropertyId,
			$config->pointInTimePropertyId ?? $this->pointInTimePropertyId,
			$config->properties ?? $this->properties,
			$config->introText ?? $this->introText,
		);
	}

	public function hasRequiredValues(): bool {
		return $this->startYearPropertyId !== null &&
			$this->endYearPropertyId !== null &&
			$this->pointInTimePropertyId !== null;
	}

}
