<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

use ProfessionalWiki\WikibaseExport\Application\PropertyIdList;
use ProfessionalWiki\WikibaseExport\Application\TimeQualifierProperties;
use ProfessionalWiki\WikibaseExport\Application\TimeQualifierStatementFilter;
use ProfessionalWiki\WikibaseExport\Application\TimeRange;
use Wikibase\DataModel\Statement\StatementFilter;
use Wikibase\DataModel\Statement\StatementList;

class YearGroupingStatementsMapper implements StatementsMapper {

	/**
	 * @var int[]
	 */
	private array $years;

	/**
	 * @var StatementFilter[]
	 */
	private array $filtersByYear;

	public function __construct(
		private ValueSetCreator $valueSetCreator,
		private PropertyIdList $yearGroupedProperties,
		private TimeQualifierProperties $timeQualifierProperties,
		private int $startYear,
		private int $endYear,
		private HeaderBuilder $headerBuilder
	) {
		$this->years = $this->buildYears();
	}

	public function createColumnHeaders(): ColumnHeaders {
		$headers = [];

		foreach ( $this->yearGroupedProperties->ids as $property ) {
			foreach ( $this->years as $year ) {
				$headers[] = new ColumnHeader( $this->headerBuilder->propertyIdToHeader( $property ) . ' ' . $year );
			}
		}

		return new ColumnHeaders( $headers );
	}

	/**
	 * @return int[]
	 */
	private function buildYears(): array {
		$years = range( $this->startYear, $this->endYear );
		arsort( $years );
		return $years;
	}

	public function buildValueSetList( StatementList $statements ): ValueSetList {
		$valueSets = [];

		foreach ( $this->yearGroupedProperties->ids as $propertyId ) {
			foreach ( $this->years as $year ) {
				$valueSets[] = $this->valueSetCreator->statementsToValueSet(
					$statements->getByPropertyId( $propertyId )->getBestStatements()->filter( $this->getFilter( $year ) )
				);
			}
		}

		return new ValueSetList( $valueSets );
	}

	private function getFilter( int $year ): StatementFilter {
		$this->filtersByYear[$year] ??= $this->newFilter( $year );
		return $this->filtersByYear[$year];
	}

	private function newFilter( int $year ): StatementFilter {
		return new TimeQualifierStatementFilter(
			timeRange: TimeRange::newFromStartAndEndYear( $year, $year ),
			qualifierProperties: $this->timeQualifierProperties
		);
	}

}
