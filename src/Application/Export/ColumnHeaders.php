<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

class ColumnHeaders {

	/**
	 * @param ColumnHeader[] $headers
	 */
	public function __construct(
		public /* readonly */ array $headers = []
	) {
		foreach ( $this->headers as $header ) {
			if ( !( $header instanceof ColumnHeader ) ) {
				throw new \InvalidArgumentException();
			}
		}
	}

	public function plus( self $headers ): self {
		return new self( array_merge( $this->headers, $headers->headers ) );
	}

}
