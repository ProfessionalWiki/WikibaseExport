<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Application\Export;

use ValueFormatters\FormatterOptions;
use Wikibase\Lib\Formatters\SnakFormatter;
use Wikibase\Repo\WikibaseRepo;

class ProductionValueSetCreatorFactory implements ValueSetCreatorFactory {

	public function newValueSetCreator( string $languageCode ): ValueSetCreator {
		return new ProductionValueSetCreator(
			snakFormatter: WikibaseRepo::getSnakFormatterFactory()->getSnakFormatter(
				SnakFormatter::FORMAT_PLAIN,
				new FormatterOptions(
					[ SnakFormatter::OPT_LANG => $languageCode ]
				)
			)
		);
	}

}
