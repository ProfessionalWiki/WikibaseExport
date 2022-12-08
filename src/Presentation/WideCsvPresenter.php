<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Presentation;

use ProfessionalWiki\WikibaseExport\Application\Export\ColumnHeader;
use ProfessionalWiki\WikibaseExport\Application\Export\ColumnHeaders;
use ProfessionalWiki\WikibaseExport\Application\Export\ExportPresenter;
use ProfessionalWiki\WikibaseExport\Application\Export\MappedEntity;
use RuntimeException;

class WideCsvPresenter implements ExportPresenter {

	/**
	 * @var resource
	 */
	private $stream;

	public function presentExportStarted( ColumnHeaders $headers ): void {
		$this->openStream();
		$this->writeHeaderRow( $headers );
	}

	private function openStream(): void {
		if ( isset( $this->stream ) ) {
			return;
		}

		$stream = fopen( 'php://temp', 'r+' );

		if ( $stream === false ) {
			throw new RuntimeException( 'Failed to open stream' );
		}

		$this->stream = $stream;
	}

	private function writeHeaderRow( ColumnHeaders $headers ): void {
		$this->writeRow(
			array_merge(
				[ 'ID', 'Label' ],
				array_map(
					fn( ColumnHeader $header ) => $header->text,
					$headers->headers
				)
			)
		);
	}

	/**
	 * @param string[] $values
	 */
	private function writeRow( array $values ): void {
		fputcsv( $this->stream, $values );
	}

	public function presentEntity( MappedEntity $entity ): void {
		$rowValues = [ $entity->id, $entity->label ];

		foreach ( $entity->valueSetList->sets as $valueSet ) {
			$rowValues[] = implode( "\n", $valueSet->values );
		}

		$this->writeRow( $rowValues );
	}

	/**
	 * @return resource
	 */
	public function getStream() {
		$this->openStream();
		return $this->stream;
	}

	public function presentInvalidRequest(): void {
		// Do nothing.
	}

	public function presentPermissionDenied(): void {
		// Do nothing.
	}

}
