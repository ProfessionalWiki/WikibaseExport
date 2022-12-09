<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application\Export;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\Export\ProductionHeaderBuilder;
use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikibase\DataModel\Services\Lookup\TermLookup;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\Export\ProductionHeaderBuilder
 */
class HeaderBuilderTest extends TestCase {

	public function testUsesLabel(): void {
		$termLookup = $this->createMock( TermLookup::class );

		$termLookup->expects( $this->once() )
			->method( 'getLabel' )
			->with( new NumericPropertyId( 'P123' ), 'en' )
			->willReturn( 'FooBar' );

		$headerBuilder = new ProductionHeaderBuilder(
			useLabelsInHeaders: true,
			termLookup: $termLookup,
			languageCode: 'en'
		);

		$this->assertSame(
			'FooBar',
			$headerBuilder->propertyIdToHeader( new NumericPropertyId( 'P123' ) )
		);
	}

	public function testUsesFallbackWhenLabelIsNull(): void {
		$termLookup = $this->createMock( TermLookup::class );

		$termLookup->method( 'getLabel' )
			->willReturn( null );

		$headerBuilder = new ProductionHeaderBuilder(
			useLabelsInHeaders: true,
			termLookup: $termLookup,
			languageCode: 'en'
		);

		$this->assertSame(
			'P123',
			$headerBuilder->propertyIdToHeader( new NumericPropertyId( 'P123' ) )
		);
	}

	public function testUsesSerializationWhenLabelIsOff(): void {
		$termLookup = $this->createMock( TermLookup::class );

		$termLookup->expects( $this->never() )
			->method( $this->anything() );

		$headerBuilder = new ProductionHeaderBuilder(
			useLabelsInHeaders: false,
			termLookup: $termLookup,
			languageCode: 'en'
		);

		$this->assertSame(
			'P123',
			$headerBuilder->propertyIdToHeader( new NumericPropertyId( 'P123' ) )
		);
	}

}
