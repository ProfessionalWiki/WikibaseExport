<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Presenation;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\Export\MappedEntity;
use ProfessionalWiki\WikibaseExport\Application\Export\MappedStatement;
use ProfessionalWiki\WikibaseExport\Application\Export\MappedYear;
use ProfessionalWiki\WikibaseExport\Presentation\WideCsvPresenter;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Presentation\WideCsvPresenter
 */
class WideCsvPresenterTest extends TestCase {

	public function testMultipleEntities(): void {
		$presenter = new WideCsvPresenter(
			years: [ 2022 ],
			properties: [ 'P1', 'P2' ]
		);

		$presenter->presentEntity( new MappedEntity(
			id: 'Q42',
			statementsByYear: [
				new MappedYear(
					year: 2022,
					statements: [
						new MappedStatement( 'P1', 'Foo' ),
						new MappedStatement( 'P2', 'Bar' ),
					]
				),
			]
		) );

		$presenter->presentEntity( new MappedEntity(
			id: 'Q43',
			statementsByYear: [
				new MappedYear(
					year: 2022,
					statements: [
						new MappedStatement( 'P2', 'MoreBar' ),
						new MappedStatement( 'P1', 'MoreFoo' ),
					]
				),
			]
		) );

		$this->assertSame(
			<<<CSV
ID,P1,P2
Q42,Foo,Bar
Q43,MoreFoo,MoreBar

CSV
			,
			$this->getCsvString( $presenter )
		);
	}

	private function getCsvString( WideCsvPresenter $presenter ): string {
		rewind( $presenter->getStream() );
		return stream_get_contents( $presenter->getStream() );
	}

	public function testMultipleValuesPerProperty(): void {
		$presenter = new WideCsvPresenter(
			years: [ 2022 ],
			properties: [ 'P1', 'P2' ]
		);

		$presenter->presentEntity( new MappedEntity(
			id: 'Q42',
			statementsByYear: [
				new MappedYear(
					year: 2022,
					statements: [
						new MappedStatement( 'P1', 'One' ),
						new MappedStatement( 'P2', 'Two' ),
						new MappedStatement( 'P2', 'Three' ),
						new MappedStatement( 'P1', 'Four' ),
					]
				),
			]
		) );

		$this->assertSame(
			<<<CSV
ID,P1,P2
Q42,"One
Four","Two
Three"

CSV
			,
			$this->getCsvString( $presenter )
		);
	}

	// TODO: test multiple years

}
