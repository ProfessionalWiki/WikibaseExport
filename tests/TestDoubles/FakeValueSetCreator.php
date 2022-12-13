<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\TestDoubles;

use ProfessionalWiki\WikibaseExport\Application\Export\ValueSet;
use ProfessionalWiki\WikibaseExport\Application\Export\ValueSetCreator;
use ProfessionalWiki\WikibaseExport\Application\Export\ValueSetCreatorFactory;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Snak\Snak;
use Wikibase\DataModel\Statement\StatementList;

class FakeValueSetCreator implements ValueSetCreator, ValueSetCreatorFactory {

	public function statementsToValueSet( StatementList $statements ): ValueSet {
		$values = [];

		foreach ( $statements->getBestStatements()->getMainSnaks() as $snak ) {
			$values[] = $this->snakToString( $snak );
		}

		return new ValueSet( $values );
	}

	private function snakToString( Snak $snak ): string {
		if ( $snak instanceof PropertyValueSnak ) {
			 return $snak->getDataValue()->serialize();
		}

		return $snak->getPropertyId()->getSerialization();
	}

	public function newValueSetCreator( string $languageCode ): ValueSetCreator {
		return $this;
	}

}
