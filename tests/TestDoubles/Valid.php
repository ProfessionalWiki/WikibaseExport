<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\TestDoubles;

class Valid {

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
    "propertiesToGroupByYear": [
        "P4",
        "P5"
    ],
    "ungroupedProperties": [
        "P6",
        "P7"
    ],
	"subjectFilterPropertyId": "P10",
	"subjectFilterPropertyValue": "company",
	"exportLanguages": [ "en", "nl" ]
}
';
	}

}
