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

	private bool $isValid = true;

	/**
	 * @param int[] $years
	 * @param string[] $properties
	 */
	public function __construct(
		private array $years,
		private array $properties
	) {
		arsort( $this->years );
	}

	public function presentEntity( MappedEntity $entity ): void {
		$rowValues = [ $entity->id ];

		foreach ( $this->properties as $propertyId ) {
			foreach ( $this->years as $year ) {
				$rowValues[] = implode(
					"\n",
					$entity->getYear( $year )->getValuesForProperty( $propertyId )
				);
			}
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
		fputcsv(
			$this->stream,
			array_merge(
				[ 'ID' ],
				...$this->buildHeadersForEachProperty()
			)
		);
	}

	/**
	 * @return array<mixed, string[]>
	 */
	private function buildHeadersForEachProperty(): array {
		return array_map(
			fn( string $propertyId ) => array_map(
				fn( int $year ) => $this->buildPropertyHeader( $propertyId, $year ),
				$this->years
			),
			$this->properties
		);
	}

	private function buildPropertyHeader( string $propertyId, int $year ): string {
		return "$propertyId $year";
	}

	/**
	 * @return resource
	 */
	public function getStream() {
		$this->initialize();
		return $this->stream;
	}

	public function presentInvalidRequest(): void {
		$this->isValid = false;
	}

	public function isValid(): bool {
		return $this->isValid;
	}

}
