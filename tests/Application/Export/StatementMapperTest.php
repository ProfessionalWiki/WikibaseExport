<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application\Export;

use DataValues\StringValue;
use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\Export\MappedStatement;
use ProfessionalWiki\WikibaseExport\Application\Export\StatementMapper;
use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\Statement;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\Export\StatementMapper
 */
class StatementMapperTest extends TestCase {

	// TODO: test NoValue and SomeValue
	// TODO: test non-string values
	// TODO: test multiple values

	public function testMapsStringValue(): void {
		$mapper = new StatementMapper();

		$this->assertEquals(
			new MappedStatement( 'foo1' ),
			$mapper->mapStatement(
				new Statement( new PropertyValueSnak( new NumericPropertyId( 'P1' ), new StringValue( 'foo1' ) ) )
			)
		);
	}

}
