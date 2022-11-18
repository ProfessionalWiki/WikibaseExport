<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

use ProfessionalWiki\WikibaseExport\Application\EntitySource;
use ProfessionalWiki\WikibaseExport\Application\ExportStatementFilter;
use ProfessionalWiki\WikibaseExport\Application\TimeQualifierProperties;
use ProfessionalWiki\WikibaseExport\Application\TimeQualifierStatementGrouper;
use ProfessionalWiki\WikibaseExport\Application\TimeRange;
use ProfessionalWiki\WikibaseExport\Persistence\IdListEntitySource;
use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikibase\Repo\WikibaseRepo;

class ExportUcFactory {

	public function buildUseCase(
		ExportRequest $request,
		ExportPresenter $presenter,
	): ExportUseCase {
		return new ExportUseCase(
			entitySource: $this->newEntitySource( $request ),
			entityMapper: $this->newEntityMapper( $request ),
			presenter: $presenter
		);
	}

	private function newEntitySource( ExportRequest $request ): EntitySource {
		return new IdListEntitySource(
			WikibaseRepo::getEntityLookup(),
			$request->subjectIds
		);
	}

	private function newEntityMapper( ExportRequest $request ): EntityMapper {
		$timeQualifierProperties = $this->newTimeQualifierProperties();

		return new EntityMapper(
			statementFilter: new ExportStatementFilter(
				propertyIds: $request->statementPropertyIds,
				timeRange: TimeRange::newFromStartAndEndYear( $request->startYear, $request->endYear ),
				qualifierProperties: $timeQualifierProperties
			),
			statementGrouper: TimeQualifierStatementGrouper::newForYearRange(
				timeQualifierProperties: $timeQualifierProperties,
				startYear: $request->startYear,
				endYear: $request->endYear
			),
			statementMapper: new StatementMapper()
		);
	}

	private function newTimeQualifierProperties(): TimeQualifierProperties {
		return new TimeQualifierProperties(
			pointInTime: new NumericPropertyId( 'P1' ), // TODO: get from config
			startTime: new NumericPropertyId( 'P1' ), // TODO: get from config
			endTime: new NumericPropertyId( 'P1' ), // TODO: get from config
		);
	}

}
