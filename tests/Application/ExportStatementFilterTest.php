<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\ExportStatementFilter;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\TimeHelper;
use Wikibase\DataModel\Entity\NumericPropertyId;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\ExportStatementFilter
 */
class ExportStatementFilterTest extends TestCase {

	public function testMatchingStatement(): void {
		$filter = new ExportStatementFilter(
			[ new NumericPropertyId( 'P1' ), new NumericPropertyId( 'P2' ) ],
			TimeHelper::newJan2000ToDec2005(),
			TimeHelper::newTimeQualifierProperties()
		);

		$statement = TimeHelper::newPointInTimeStatement( day: '2001-01-01' );

		$this->assertTrue( $filter->statementMatches( $statement ) );
	}

}
