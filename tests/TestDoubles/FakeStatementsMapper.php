<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\TestDoubles;

use ProfessionalWiki\WikibaseExport\Application\Export\ColumnHeader;
use ProfessionalWiki\WikibaseExport\Application\Export\ColumnHeaders;
use ProfessionalWiki\WikibaseExport\Application\Export\StatementsMapper;
use ProfessionalWiki\WikibaseExport\Application\Export\ValueSet;
use ProfessionalWiki\WikibaseExport\Application\Export\ValueSetList;
use Wikibase\DataModel\Snak\Snak;
use Wikibase\DataModel\Statement\StatementList;

class FakeStatementsMapper implements StatementsMapper {

	public function __construct(
		private string $header
	) {
	}

	public function createColumnHeaders(): ColumnHeaders {
		return new ColumnHeaders( [
			new ColumnHeader( $this->header ),
		] );
	}

	public function buildValueSetList( StatementList $statements ): ValueSetList {
		return new ValueSetList( [
			new ValueSet( array_map(
				fn( Snak $snak ) => $this->header . ' ' . $snak->getPropertyId()->getSerialization(),
				$statements->getMainSnaks()
			) )
		] );
	}

}
