<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

use ProfessionalWiki\WikibaseExport\Application\EntitySource;
use ProfessionalWiki\WikibaseExport\Application\EntitySourceFactory;
use ProfessionalWiki\WikibaseExport\Application\PropertyIdList;
use ProfessionalWiki\WikibaseExport\Application\TimeQualifierProperties;
use Wikibase\DataModel\Services\Lookup\TermLookup;

class ExportUseCase {

	public function __construct(
		private PropertyIdList $ungroupedProperties,
		private PropertyIdList $propertiesGroupedByYear,
		private TimeQualifierProperties $timeQualifierProperties,
		private EntitySourceFactory $entitySourceFactory,
		private ExportPresenter $presenter,
		private ExportAuthorizer $authorizer,
		private ValueSetCreatorFactory $valueSetCreatorFactory,
		private TermLookup $termLookup
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

		$headerBuilder = $this->newHeaderBuilder( $request );

		$simpleMapper = $this->newSimpleMapper( $request, $headerBuilder );
		$yearlyGroupingMapper = $this->newYearlyGroupingMapper( $request, $headerBuilder );

		$this->exportHeaders( $simpleMapper, $yearlyGroupingMapper );
		$this->exportEntities( $request, $simpleMapper, $yearlyGroupingMapper );
	}

	private function requestIsValid( ExportRequest $request ): bool {
		return $request->startYear <= $request->endYear
			&& $request->endYear - $request->startYear <= 100;
	}

	private function newHeaderBuilder( ExportRequest $request ): HeaderBuilder {
		return new ProductionHeaderBuilder(
			useLabelsInHeaders: $request->useLabelsInHeaders,
			termLookup: $this->termLookup,
			languageCode: $request->languageCode
		);
	}

	private function newSimpleMapper( ExportRequest $request, HeaderBuilder $headerBuilder ): StatementsMapper {
		return new SimpleStatementsMapper(
			valueSetCreator: $this->valueSetCreatorFactory->newValueSetCreator( $request->languageCode ),
			propertyIds: $request->ungroupedStatementPropertyIds->intersect( $this->ungroupedProperties ),
			headerBuilder: $headerBuilder
		);
	}

	private function newYearlyGroupingMapper( ExportRequest $request, HeaderBuilder $headerBuilder ): StatementsMapper {
		return new YearGroupingStatementsMapper(
			valueSetCreator: $this->valueSetCreatorFactory->newValueSetCreator( $request->languageCode ),
			yearGroupedProperties: $request->groupedStatementPropertyIds->intersect( $this->propertiesGroupedByYear ),
			timeQualifierProperties: $this->timeQualifierProperties,
			startYear: $request->startYear,
			endYear: $request->endYear,
			headerBuilder: $headerBuilder
		);
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
