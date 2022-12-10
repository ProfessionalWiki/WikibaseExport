<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application;

use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikibase\DataModel\Entity\PropertyId;

/**
 * @psalm-immutable
 */
class PropertyIdList {

	/**
	 * @var PropertyId[]
	 */
	public /* readonly */ array $ids;

	/**
	 * @param PropertyId[] $ids
	 */
	public function __construct( array $ids = [] ) {
		$this->ids = array_values( $ids );
	}

	public function intersect( self $ids ): self {
		return new self(
			array_map(
				fn ( string $id ) => new NumericPropertyId( $id ),
				array_intersect(
					array_map( fn( PropertyId $id ) => $id->getSerialization(), $this->ids ),
					array_map( fn( PropertyId $id ) => $id->getSerialization(), $ids->ids ),
				)
			)
		);
	}

	public function isEmpty(): bool {
		return $this->ids === [];
	}

}
