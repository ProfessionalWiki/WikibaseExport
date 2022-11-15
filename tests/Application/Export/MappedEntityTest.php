<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application\Export;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\Export\MappedEntity;
use ProfessionalWiki\WikibaseExport\Application\Export\MappedStatement;
use ProfessionalWiki\WikibaseExport\Application\Export\MappedYear;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\Export\MappedEntity
 * @covers \ProfessionalWiki\WikibaseExport\Application\Export\MappedYear
 * @covers \ProfessionalWiki\WikibaseExport\Application\Export\MappedStatement
 */
class MappedEntityTest extends TestCase {

	public function testGetExistingYear(): void {
		$year = new MappedYear(
			year: 2022,
			statements: [ new MappedStatement( 'FooBar' ) ]
		);

		$entity = new MappedEntity(
			id: 'Q42',
			statementsByYear: [ $year ]
		);

		$this->assertEquals(
			$year,
			$entity->getYear( 2022 )
		);
	}

	public function testGetUnknownYear(): void {
		$entity = new MappedEntity(
			id: 'Q42',
			statementsByYear: [
				new MappedYear(
					year: 2022,
					statements: [ new MappedStatement( 'FooBar' ) ]
				)
			]
		);

		$this->assertEquals(
			new MappedYear(
				year: 2045,
				statements: []
			),
			$entity->getYear( 2045 )
		);
	}

}
