<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\TestDoubles;

use Wikibase\DataModel\Term\Fingerprint;
use Wikibase\DataModel\Term\Term;
use Wikibase\DataModel\Term\TermList;

class EntityHelper {

	public static function newLabelFingerprint( string $label ): Fingerprint {
		return new Fingerprint( new Termlist( [ new Term( 'en', $label ) ] ) );
	}

}
