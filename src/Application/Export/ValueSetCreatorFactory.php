<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

interface ValueSetCreatorFactory {

	public function newValueSetCreator( string $languageCode ): ValueSetCreator;

}
