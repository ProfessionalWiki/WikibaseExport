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

		$this->assertPresenterHasCsv(
			<<<CSV
ID,"P1 2022","P2 2022"
Q42,Foo,Bar
Q43,MoreFoo,MoreBar

CSV,
			$presenter
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

		$this->assertPresenterHasCsv(
			<<<CSV
ID,"P1 2022","P2 2022"
Q42,"One
Four","Two
Three"

CSV,
			$presenter
		);
	}

	private function assertPresenterHasCsv( string $expected, WideCsvPresenter $presenter ): void {
		$this->assertSame(
			$expected,
			$this->getCsvString( $presenter )
		);
	}

	public function testMultipleYears(): void {
		$presenter = new WideCsvPresenter(
			years: [ 2022, 2023 ],
			properties: [ 'P1', 'P2' ]
		);

		$presenter->presentEntity( new MappedEntity(
			id: 'Q42',
			statementsByYear: [
				new MappedYear(
					year: 2022,
					statements: [
						new MappedStatement( 'P1', 'P1_2022' ),
						new MappedStatement( 'P2', 'P2_2022' ),
					]
				),
				new MappedYear(
					year: 2023,
					statements: [
						new MappedStatement( 'P1', 'P1_2023' ),
						new MappedStatement( 'P2', 'P2_2023' ),
					]
				),
			]
		) );

		$this->assertPresenterHasCsv(
			<<<CSV
ID,"P1 2023","P1 2022","P2 2023","P2 2022"
Q42,P1_2023,P1_2022,P2_2023,P2_2022

CSV,
			$presenter
		);
	}

	public function testIsNoYearsMatchTheResultIsEmpty(): void {
		$presenter = new WideCsvPresenter(
			years: [ 2022 ],
			properties: [ 'P1', 'P2' ]
		);

		$presenter->presentEntity( new MappedEntity(
			id: 'Q42',
			statementsByYear: [
				new MappedYear(
					year: 2021,
					statements: [
						new MappedStatement( 'P1', 'P1_2021' ),
						new MappedStatement( 'P2', 'P2_2021' ),
					]
				),
				new MappedYear(
					year: 2023,
					statements: [
						new MappedStatement( 'P1', 'P1_2023' ),
						new MappedStatement( 'P2', 'P2_2023' ),
					]
				),
			]
		) );

		$presenter->presentEntity( new MappedEntity(
			id: 'Q41',
			statementsByYear: []
		) );

		$this->assertPresenterHasCsv(
			<<<CSV
ID,"P1 2022","P2 2022"
Q42,,
Q41,,

CSV,
			$presenter
		);
	}

}
