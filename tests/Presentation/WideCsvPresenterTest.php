<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Presenation;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\Export\ColumnHeader;
use ProfessionalWiki\WikibaseExport\Application\Export\ColumnHeaders;
use ProfessionalWiki\WikibaseExport\Application\Export\MappedEntity;
use ProfessionalWiki\WikibaseExport\Application\Export\ValueSet;
use ProfessionalWiki\WikibaseExport\Application\Export\ValueSetList;
use ProfessionalWiki\WikibaseExport\Presentation\WideCsvPresenter;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Presentation\WideCsvPresenter
 */
class WideCsvPresenterTest extends TestCase {

	public function testMultipleEntities(): void {
		$presenter = new WideCsvPresenter();

		$presenter->presentExportStarted( new ColumnHeaders( [
			new ColumnHeader( 'P1 2022' ),
			new ColumnHeader( 'P2 2022' ),
		] ) );

		$presenter->presentEntity( new MappedEntity(
			id: 'Q42',
			label: 'Item One',
			valueSetList: new ValueSetList( [
				new ValueSet( [ 'Foo' ] ),
				new ValueSet( [ 'Bar' ] ),
			] ),
		) );

		$presenter->presentEntity( new MappedEntity(
			id: 'Q43',
			label: 'Item Two',
			valueSetList: new ValueSetList( [
				new ValueSet( [ 'MoreFoo' ] ),
				new ValueSet( [ 'MoreBar' ] ),
			] ),
		) );

		$this->assertPresenterHasCsv(
			<<<CSV
ID,Label,"P1 2022","P2 2022"
Q42,"Item One",Foo,Bar
Q43,"Item Two",MoreFoo,MoreBar

CSV,
			$presenter
		);
	}

	private function getCsvString( WideCsvPresenter $presenter ): string {
		rewind( $presenter->getStream() );
		return stream_get_contents( $presenter->getStream() );
	}

	public function testMultipleValuesPerProperty(): void {
		$presenter = new WideCsvPresenter();

		$presenter->presentExportStarted( new ColumnHeaders( [
			new ColumnHeader( 'P1 2022' ),
			new ColumnHeader( 'P2 2022' ),
		] ) );

		$presenter->presentEntity( new MappedEntity(
			id: 'Q42',
			label: 'Foo Bar',
			valueSetList: new ValueSetList( [
				new ValueSet( [ 'One', 'Four' ] ),
				new ValueSet( [ 'Two', 'Three' ] ),
			] ),
		) );

		$this->assertPresenterHasCsv(
			<<<CSV
ID,Label,"P1 2022","P2 2022"
Q42,"Foo Bar","One
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

}
