<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\TestDoubles;

use DataValues\StringValue;
use RuntimeException;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Snak\Snak;
use Wikibase\Lib\Formatters\SnakFormatter;

class FakeSnakFormatter implements SnakFormatter {

	public function formatSnak( Snak $snak ): string {
		if ( $snak instanceof PropertyValueSnak ) {
			$value = $snak->getDataValue();

			if ( $value instanceof StringValue ) {
				return $value->getValue();
			}
		}

		throw new RuntimeException( 'This test double only supports PropertyValueSnak+StringValue' );
	}

	public function getFormat(): string {
		return SnakFormatter::FORMAT_PLAIN;
	}

}
