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

	public static function configJson(): string {
		return '
{
    "defaultSubjects": [
        "Q1",
        "Q2"
    ],
    "defaultStartYear": 2010,
    "defaultEndYear": 2022,
    "startTimePropertyId": "P1",
    "endTimePropertyId": "P2",
    "pointInTimePropertyId": "P3",
    "properties": [
        "P4",
        "P5"
    ],
	"subjectFilterPropertyId": "P10",
	"subjectFilterPropertyValue": "company"
}
';
	}

}
