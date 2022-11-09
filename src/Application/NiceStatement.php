<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application;

use DataValues\DataValue;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Snak\Snak;
use Wikibase\DataModel\Statement\Statement;

/**
 * Wrapper around Statement that provides convenient generic accessor methods.
 */
class NiceStatement {

	private Statement $statement;

	public function __construct( Statement $statement ) {
		$this->statement = $statement;
	}

	public function getQualifierValue( PropertyId $propertyId ): ?DataValue {
		/**
		 * @var Snak $qualifier
		 */
		foreach ( $this->statement->getQualifiers() as $qualifier ) {
			if ( $qualifier instanceof PropertyValueSnak ) {
				if ( $qualifier->getPropertyId()->equals( $propertyId ) ) {
					return $qualifier->getDataValue();
				}
			}
		}

		return null;
	}

//	public function getQualifierStringValue( PropertyId $propertyId ): ?string {
//		$dataValue = $this->getQualifierValue( $propertyId );
//
//		if ( $dataValue instanceof StringValue ) {
//			return $dataValue->getValue();
//		}
//
//		return null;
//	}

}
