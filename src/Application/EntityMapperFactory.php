<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application;

use ProfessionalWiki\WikibaseExport\Application\Export\EntityMapper;
use ProfessionalWiki\WikibaseExport\Application\Export\StatementMapper;
use Wikibase\DataModel\Entity\PropertyId;

class EntityMapperFactory {

	public function __construct(
		private TimeQualifierProperties $timeQualifierProperties,
		private StatementMapper $statementMapper,
		private string $contentLanguage
	) {
	}

	/**
	 * @param PropertyId[] $basicProperties
	 * @param PropertyId[] $propertiesGroupedByYear
	 */
	public function newEntityMapper( array $basicProperties, array $propertiesGroupedByYear, int $startYear, int $endYear ): EntityMapper {
		return new EntityMapper(
			statementFilter: new ExportStatementFilter(
				alwaysIncludedProperties: $basicProperties,
				timeQualifiedProperties: $propertiesGroupedByYear,
				timeRange: TimeRange::newFromStartAndEndYear( $startYear, $endYear ),
				qualifierProperties: $this->timeQualifierProperties
			),
			statementGrouper: TimeQualifierStatementGrouper::newForYearRange(
				timeQualifierProperties: $this->timeQualifierProperties,
				startYear: $startYear,
				endYear: $endYear
			),
			statementMapper: $this->statementMapper,
			contentLanguage: $this->contentLanguage
		);
	}

}
