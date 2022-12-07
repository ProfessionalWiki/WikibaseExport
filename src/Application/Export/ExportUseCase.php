<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

use ProfessionalWiki\WikibaseExport\Application\EntityMapperFactory;
use ProfessionalWiki\WikibaseExport\Application\EntitySource;
use ProfessionalWiki\WikibaseExport\Application\EntitySourceFactory;
use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikibase\DataModel\Entity\PropertyId;

class ExportUseCase {

	/**
	 * @param PropertyId[] $ungroupedProperties
	 * @param PropertyId[] $propertiesGroupedByYear
	 */
	public function __construct(
		private array $ungroupedProperties,
		private array $propertiesGroupedByYear,
		private EntitySourceFactory $entitySourceFactory,
		private EntityMapperFactory $entityMapperFactory,
		private ExportPresenter $presenter,
		private ExportAuthorizer $authorizer
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

		$entitySource = $this->newEntitySource( $request );
		$entityMapper = $this->newEntityMapper( $request );

		while ( true ) {
			$entity = $entitySource->next();

			if ( $entity === null ) {
				break;
			}

			$this->presenter->presentEntity( $entityMapper->map( $entity ) );
		}
	}

	private function newEntitySource( ExportRequest $request ): EntitySource {
		return $this->entitySourceFactory->newEntitySource( $request->subjectIds );
	}

	private function newEntityMapper( ExportRequest $request ): EntityMapper {
		return $this->entityMapperFactory->newEntityMapper(
			$this->idsInBoth( $this->ungroupedProperties, $request->statementPropertyIds ),
			$this->idsInBoth( $this->propertiesGroupedByYear, $request->statementPropertyIds ),
			$request->startYear,
			$request->endYear
		);
	}

	/**
	 * @param PropertyId[] $a
	 * @param PropertyId[] $b
	 *
	 * @return PropertyId[]
	 */
	private function idsInBoth( array $a, array $b ): array {
		return array_map(
			fn ( string $id ) => new NumericPropertyId( $id ),
			array_intersect(
				array_map( fn( PropertyId $id ) => $id->getSerialization(), $a ),
				array_map( fn( PropertyId $id ) => $id->getSerialization(), $b ),
			)
		);
	}

	private function requestIsValid( ExportRequest $request ): bool {
		return $request->startYear <= $request->endYear
			&& $request->endYear - $request->startYear <= 100;
	}

}
