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
    "entityLabelLanguage": "en",
    "chooseSubjectsLabel": "choose foo",
    "filterSubjectsLabel": "filter foo",
    "defaultSubjects": [
        "Q1",
        "Q2"
    ],
    "defaultStartYear": 2010,
    "defaultEndYear": 2022,
    "startYearPropertyId": "P1",
    "endYearPropertyId": "P2",
    "pointInTimePropertyId": "P3",
    "properties": [
        "P4",
        "P5"
    ],
    "introText": "Lorem ipsum"
}
';
	}

}
