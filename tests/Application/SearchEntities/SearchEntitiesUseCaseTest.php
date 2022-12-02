<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application\Export;

use DataValues\StringValue;
use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\SearchEntities\SearchEntitiesPresenter;
use ProfessionalWiki\WikibaseExport\Application\SearchEntities\SearchEntitiesUseCase;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\EntityHelper;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\StubEntitySearchHelper;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\SpySearchEntitiesPresenter;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikibase\DataModel\Services\Lookup\InMemoryEntityLookup;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\DataModel\Statement\StatementList;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\SearchEntities\SearchEntitiesUseCase
 */
class SearchEntitiesUseCaseTest extends TestCase {

	private const INSTANCE_OF_ID = 'P1';
	private const INSTANCE_OF_VALUE = 'company';

	private function createPerson( string $id, string $label ): Item {
		return new Item(
			id: new ItemId( $id ),
			fingerprint: EntityHelper::newLabelFingerprint( $label )
		);
	}

	private function createCompany( string $id, string $label ): Item {
		return new Item(
			id: new ItemId( $id ),
			fingerprint: EntityHelper::newLabelFingerprint( $label ),
			statements: new StatementList(
				new Statement(
					mainSnak: new PropertyValueSnak(
						new NumericPropertyId( self::INSTANCE_OF_ID ),
						new StringValue( self::INSTANCE_OF_VALUE )
					)
				)
			)
		);
	}

	/**
	 * @return EntityDocument[]
	 */
	private function getAllEntities(): array {
		return [
			$this->createPerson( 'Q1', 'John Doe' ),
			$this->createCompany( 'Q3', 'Company Foo' ),
			$this->createCompany( 'Q4', 'Company Not Foo' ),
			$this->createCompany( 'Q5', 'Company Foo Bar' ),
			$this->createCompany( 'Q6', 'Company Bar' ),
			$this->createPerson( 'Q10', 'Joe Bloggs' )
		];
	}

	private function newSearchEntitiesUseCase(
		array $searchEntities,
		SearchEntitiesPresenter $presenter,
		?string $propertyId = self::INSTANCE_OF_ID,
		?string $propertyValue = self::INSTANCE_OF_VALUE
	): SearchEntitiesUseCase {
		return new SearchEntitiesUseCase(
			subjectFilterPropertyId: $propertyId,
			subjectFilterPropertyValue: $propertyValue,
			entitySearchHelper: new StubEntitySearchHelper( ...$searchEntities ),
			contentLanguage: 'en',
			entityLookup: new InMemoryEntityLookup( ...$this->getAllEntities() ),
			presenter: $presenter
		);
	}

	public function testResultsAreNotFilteredWhenPropertyIdIsNull(): void {
		$searchEntities = [
			$this->createPerson( 'Q1', 'John Doe' ),
			$this->createPerson( 'Q10', 'Joe Bloggs' )
		];

		$presenter = new SpySearchEntitiesPresenter();
		$searcher = $this->newSearchEntitiesUseCase( $searchEntities, $presenter, propertyId: null );
		$searcher->search( 'Jo' );

		$this->assertSame(
			[
				[ 'id' => 'Q1', 'label' => 'John Doe' ],
				[ 'id' => 'Q10', 'label' => 'Joe Bloggs' ]
			],
			$presenter->searchResult
		);
	}

	public function testResultsAreNotFilteredWhenPropertyValueIsNull(): void {
		$searchEntities = [
			$this->createPerson( 'Q1', 'John Doe' ),
			$this->createPerson( 'Q10', 'Joe Bloggs' )
		];

		$presenter = new SpySearchEntitiesPresenter();
		$searcher = $this->newSearchEntitiesUseCase( $searchEntities, $presenter, propertyValue: null );
		$searcher->search( 'Jo' );

		$this->assertSame(
			[
				[ 'id' => 'Q1', 'label' => 'John Doe' ],
				[ 'id' => 'Q10', 'label' => 'Joe Bloggs' ]
			],
			$presenter->searchResult
		);
	}

	public function testNoResultsFoundWithoutFiltering(): void {
		$presenter = new SpySearchEntitiesPresenter();
		$searcher = $this->newSearchEntitiesUseCase( [], $presenter, propertyId: null );
		$searcher->search( 'Nothing' );

		$this->assertSame(
			[],
			$presenter->searchResult
		);
	}

	public function testNoResultsFoundWithFiltering(): void {
		$seachEntites = [
			$this->createPerson( 'Q1', 'John Doe' ),
			$this->createPerson( 'Q10', 'Joe Bloggs' )
		];

		$presenter = new SpySearchEntitiesPresenter();
		$searcher = $this->newSearchEntitiesUseCase( $seachEntites, $presenter );
		$searcher->search( 'Jo' );

		$this->assertSame(
			[],
			$presenter->searchResult
		);
	}

	public function testResultsFoundWithFiltering(): void {
		$searchEntities = [
			$this->createCompany( 'Q3', 'Company Foo' ),
			$this->createCompany( 'Q5', 'Company Foo Bar' ),
		];

		$presenter = new SpySearchEntitiesPresenter();
		$searcher = $this->newSearchEntitiesUseCase( $searchEntities, $presenter );
		$searcher->search( 'Company F' );

		$this->assertSame(
			[
				[ 'id' => 'Q3', 'label' => 'Company Foo' ],
				[ 'id' => 'Q5', 'label' => 'Company Foo Bar' ],
			],
			$presenter->searchResult
		);
	}

}
