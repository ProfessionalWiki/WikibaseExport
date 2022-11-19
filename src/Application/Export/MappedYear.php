<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

class MappedYear {

	/**
	 * @var array<string, array<int, string>>
	 */
	private array $valuesPerProperty = [];

	/**
	 * @param MappedStatement[] $statements
	 */
	public function __construct(
		public /* readonly */ int $year,
		array $statements
	) {
		foreach ( $statements as $statement ) {
			$this->valuesPerProperty[$statement->propertyId][] = $statement->mainValue;
		}
	}

	/**
	 * @return string[]
	 */
	public function getValuesForProperty( string $propertyId ): array {
		return $this->valuesPerProperty[$propertyId] ?? [];
	}

	/**
	 * @return array<string, array<int, string>>
	 */
	public function getAllValuesPerProperty(): array {
		return $this->valuesPerProperty;
	}

}
