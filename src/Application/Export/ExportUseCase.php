<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

use ProfessionalWiki\WikibaseExport\Application\EntitySource;
use ProfessionalWiki\WikibaseExport\Application\EntitySourceFactory;
use ProfessionalWiki\WikibaseExport\Application\PropertyIdList;
use ProfessionalWiki\WikibaseExport\Application\TimeQualifierProperties;

class ExportUseCase {

	public function __construct(
		private PropertyIdList $ungroupedProperties,
		private PropertyIdList $propertiesGroupedByYear,
		private TimeQualifierProperties $timeQualifierProperties,
		private EntitySourceFactory $entitySourceFactory,
		private ExportPresenter $presenter,
		private ExportAuthorizer $authorizer,
		private ValueSetCreator $valueSetCreator
	) {
	}

	public function export( ExportRequest $request ): void {
		if ( !$this->authorizer->canExport() ) {
			$this->presenter->presentPermissionDenied();
			return;
		}

		if ( !$this->requestIsValid( $request ) ) {
			$this->presenter->presentInvalidRequest();
			return;
		}

		$simpleMapper = new SimpleStatementsMapper(
			valueSetCreator: $this->valueSetCreator,
			propertyIds: $request->statementPropertyIds->intersect( $this->ungroupedProperties )
		);

		$yearlyGroupingMapper = new YearGroupingStatementsMapper(
			valueSetCreator: $this->valueSetCreator,
			yearGroupedProperties: $request->statementPropertyIds->intersect( $this->propertiesGroupedByYear ),
			timeQualifierProperties: $this->timeQualifierProperties,
			startYear: $request->startYear,
			endYear: $request->endYear
		);

		$this->exportHeaders( $simpleMapper, $yearlyGroupingMapper );
		$this->exportEntities( $request, $simpleMapper, $yearlyGroupingMapper );
	}

	private function requestIsValid( ExportRequest $request ): bool {
		return $request->startYear <= $request->endYear
			&& $request->endYear - $request->startYear <= 100;
	}

	private function exportHeaders( StatementsMapper ...$mappers ): void {
		$this->presenter->presentExportStarted(
			array_reduce(
				$mappers,
				fn( ColumnHeaders $c, StatementsMapper $mapper ) => $c->plus( $mapper->createColumnHeaders() ),
				new ColumnHeaders()
			)
		);
	}

	private function exportEntities( ExportRequest $request, StatementsMapper ...$mappers ): void {
		$entityMapper = new EntityMapper(
			languageCode: $request->languageCode,
			statementsMappers: $mappers
		);

		$entitySource = $this->newEntitySource( $request );

		while ( true ) {
			$entity = $entitySource->next();

			if ( $entity === null ) {
				break;
			}

			$this->presenter->presentEntity(
				$entityMapper->map(
					entity: $entity
				)
			);
		}
	}

	private function newEntitySource( ExportRequest $request ): EntitySource {
		return $this->entitySourceFactory->newEntitySource( $request->subjectIds );
	}

}
