<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Presentation;

use ProfessionalWiki\WikibaseExport\Application\Export\ExportPresenter;
use ProfessionalWiki\WikibaseExport\Application\Export\MappedEntity;
use RuntimeException;

class WideCsvPresenter implements ExportPresenter {

	private bool $initialized = false;

	/**
	 * @var resource
	 */
	private $stream;

	/**
	 * @param int[] $years
	 * @param string[] $properties
	 */
	public function __construct(
		private array $years,
		private array $properties
	) {
	}

	public function presentEntity( MappedEntity $entity ): void {
		$rowValues = [ $entity->id ];

		foreach ( $this->properties as $propertyId ) {
			// TODO: handle all years
			$rowValues[] = implode(
				"\n",
				$entity->getYear( $this->years[0] )->getValuesForProperty( $propertyId )
			);
		}

		$this->writeRow( $rowValues );
	}

	/**
	 * @param string[] $values
	 */
	private function writeRow( array $values ): void {
		$this->initialize();
		fputcsv( $this->stream, $values );
	}

	private function initialize(): void {
		if ( $this->initialized ) {
			return;
		}

		$this->initialized = true;

		$stream = fopen( 'php://temp', 'r+' );

		if ( $stream === false ) {
			throw new RuntimeException( 'Failed to open stream' );
		}

		$this->stream = $stream;
		$this->writeHeader();
	}

	private function writeHeader(): void {
		fputcsv( $this->stream, array_merge( [ 'ID' ], $this->properties ) ); // TODO: labels instead of IDs
	}

	/**
	 * @return resource
	 */
	public function getStream() {
		$this->initialize();
		return $this->stream;
	}

}
