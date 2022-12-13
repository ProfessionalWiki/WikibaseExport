<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application;

use Exception;
use RuntimeException;
use Wikibase\DataModel\Entity\PropertyId;

class Config {

	/**
	 * @param string[]|null $defaultSubjects
	 * @param PropertyIdList|null $propertiesGroupedByYear
	 * @param PropertyIdList|null $ungroupedProperties
	 * @param string[]|null $exportLanguages
	 */
	public function __construct(
		public /* readonly */ ?array $defaultSubjects = null,
		public /* readonly */ ?int $defaultStartYear = null,
		public /* readonly */ ?int $defaultEndYear = null,
		public /* readonly */ ?PropertyId $startTimePropertyId = null,
		public /* readonly */ ?PropertyId $endTimePropertyId = null,
		public /* readonly */ ?PropertyId $pointInTimePropertyId = null,
		public /* readonly */ ?PropertyIdList $propertiesGroupedByYear = null,
		public /* readonly */ ?PropertyIdList $ungroupedProperties = null,
		public /* readonly */ ?string $subjectFilterPropertyId = null,
		public /* readonly */ ?string $subjectFilterPropertyValue = null,
		public /* readonly */ ?array $exportLanguages = null
	) {
	}

	public function combine( Config $config ): self {
		return new Config(
			$config->defaultSubjects ?? $this->defaultSubjects,
			$config->defaultStartYear ?? $this->defaultStartYear,
			$config->defaultEndYear ?? $this->defaultEndYear,
			$config->startTimePropertyId ?? $this->startTimePropertyId,
			$config->endTimePropertyId ?? $this->endTimePropertyId,
			$config->pointInTimePropertyId ?? $this->pointInTimePropertyId,
			$config->propertiesGroupedByYear ?? $this->propertiesGroupedByYear,
			$config->ungroupedProperties ?? $this->ungroupedProperties,
			$config->subjectFilterPropertyId ?? $this->subjectFilterPropertyId,
			$config->subjectFilterPropertyValue ?? $this->subjectFilterPropertyValue,
			$config->exportLanguages ?? $this->exportLanguages
		);
	}

	public function getPropertiesGroupedByYear(): PropertyIdList {
		return $this->propertiesGroupedByYear ?? new PropertyIdList();
	}

	public function getUngroupedProperties(): PropertyIdList {
		return $this->ungroupedProperties ?? new PropertyIdList();
	}

}
