<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application;

use Exception;
use RuntimeException;

class Config {

	/**
	 * @param string[]|null $defaultSubjects
	 * @param string[]|null $propertiesWithQualifiers
	 * @param string[]|null $propertiesWithoutQualifiers
	 */
	public function __construct(
		public /* readonly */ ?array $defaultSubjects = null,
		public /* readonly */ ?int $defaultStartYear = null,
		public /* readonly */ ?int $defaultEndYear = null,
		public /* readonly */ ?string $startTimePropertyId = null,
		public /* readonly */ ?string $endTimePropertyId = null,
		public /* readonly */ ?string $pointInTimePropertyId = null,
		public /* readonly */ ?array $propertiesWithQualifiers = null,
		public /* readonly */ ?array $propertiesWithoutQualifiers = null,
		public /* readonly */ ?string $subjectFilterPropertyId = null,
		public /* readonly */ ?string $subjectFilterPropertyValue = null
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
			$config->propertiesWithQualifiers ?? $this->propertiesWithQualifiers,
			$config->propertiesWithoutQualifiers ?? $this->propertiesWithoutQualifiers,
			$config->subjectFilterPropertyId ?? $this->subjectFilterPropertyId,
			$config->subjectFilterPropertyValue ?? $this->subjectFilterPropertyValue
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

	public function isComplete(): bool {
		try {
			$this->getStartTimePropertyId();
			$this->getEndTimePropertyId();
			$this->getPointInTimePropertyId();
			return true;
		} catch ( Exception ) {
			return false;
		}
	}

	/**
	 * @return string[]
	 */
	public function getAllProperties(): array {
		return array_merge( $this->propertiesWithQualifiers ?? [], $this->propertiesWithoutQualifiers ?? [] );
	}

}
