<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

/**
 * @psalm-immutable
 */
class MappedEntity {

	/**
	 * @var MappedYear[]
	 */
	private array $years;

	/**
	 * @param MappedYear[] $statementsByYear
	 */
	public function __construct(
		public /* readonly */ string $id,
		array $statementsByYear
	) {
		foreach ( $statementsByYear as $year ) {
			$this->years[$year->year] = $year;
		}
	}

	public function getYear( int $year ): MappedYear {
		return $this->years[$year] ?? new MappedYear( year: $year, statements: [] );
	}

}
