<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\TestDoubles;

use ProfessionalWiki\WikibaseExport\Application\Export\MappedEntity;
use ProfessionalWiki\WikibaseExport\Application\Export\MappedYear;

class Valid {

	/**
	 * @param MappedYear[] $statementsByYear
	 */
	public static function mappedEntity(
		string $id = 'Q1000',
		array $statementsByYear = []
	): MappedEntity {
		return new MappedEntity(
			id: $id,
			statementsByYear: $statementsByYear
		);
	}

}
