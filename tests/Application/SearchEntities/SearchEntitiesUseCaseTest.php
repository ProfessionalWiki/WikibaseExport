<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application\Export;

use DataValues\StringValue;
use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\SearchEntities\SearchEntitiesPresenter;
use ProfessionalWiki\WikibaseExport\Application\SearchEntities\SearchEntitiesUseCase;
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
use Wikibase\DataModel\Term\Term;
use Wikibase\Lib\Interactors\TermSearchResult;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\SearchEntities\SearchEntitiesUseCase
 */
class SearchEntitiesUseCaseTest extends TestCase {

	private const LANGUAGE = 'en';
	private const INSTANCE_OF_ID = 'P1';
	private const INSTANCE_OF_VALUE = 'company';

	private function createTermSearchResult( string $id, string $label ): TermSearchResult {
		return new TermSearchResult(
			matchedTerm: new Term( self::LANGUAGE, 'Jo' ),
			matchedTermType: 'match',
			entityId: new ItemId( $id ),
			displayLabel: new Term( self::LANGUAGE, $label )
		);
	}

	private function getAllSearchResults(): array {
		return [
			// People
			$this->createTermSearchResult( 'Q1', 'John Doe' ),
			$this->createTermSearchResult( 'Q2', 'Joe Bloggs' ),
			// Companies
			$this->createTermSearchResult( 'Q10', 'Joe Builder Inc.' ),
			$this->createTermSearchResult( 'Q11', 'Jolly Construction Ltd.' )
		];
	}

	private function getPeopleSearchResults(): array {
		return [
			$this->createTermSearchResult( 'Q1', 'John Doe' ),
			$this->createTermSearchResult( 'Q2', 'Joe Bloggs' ),
		];
	}

	private function createPerson( string $id ): Item {
		return new Item( new ItemId( $id ) );
	}

	private function createCompany( string $id ): Item {
		return new Item(
			id: new ItemId( $id ),
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
			$this->createPerson( 'Q1' ),
			$this->createPerson( 'Q2' ),
			$this->createCompany( 'Q10' ),
			$this->createCompany( 'Q11' ),
		];
	}

	private function newSearchEntitiesUseCase(
		array $searchResults,
		SearchEntitiesPresenter $presenter,
		?string $propertyId = self::INSTANCE_OF_ID,
		?string $propertyValue = self::INSTANCE_OF_VALUE
	): SearchEntitiesUseCase {
		return new SearchEntitiesUseCase(
			subjectFilterPropertyId: $propertyId,
			subjectFilterPropertyValue: $propertyValue,
			entitySearchHelper: new StubEntitySearchHelper( ...$searchResults ),
			contentLanguage: 'en',
			entityLookup: new InMemoryEntityLookup( ...$this->getAllEntities() ),
			presenter: $presenter
		);
	}

	public function testResultsAreNotFilteredWhenPropertyIdIsNull(): void {
		$presenter = new SpySearchEntitiesPresenter();
		$searcher = $this->newSearchEntitiesUseCase( $this->getAllSearchResults(), $presenter, propertyId: null );
		$searcher->search( 'Jo' );

		$this->assertSame(
			[
				[ 'id' => 'Q1', 'label' => 'John Doe' ],
				[ 'id' => 'Q2', 'label' => 'Joe Bloggs' ],
				[ 'id' => 'Q10', 'label' => 'Joe Builder Inc.' ],
				[ 'id' => 'Q11', 'label' => 'Jolly Construction Ltd.' ]
			],
			$presenter->searchResult
		);
	}

	public function testResultsAreNotFilteredWhenPropertyValueIsNull(): void {
		$presenter = new SpySearchEntitiesPresenter();
		$searcher = $this->newSearchEntitiesUseCase( $this->getAllSearchResults(), $presenter, propertyValue: null );
		$searcher->search( 'Jo' );

		$this->assertSame(
			[
				[ 'id' => 'Q1', 'label' => 'John Doe' ],
				[ 'id' => 'Q2', 'label' => 'Joe Bloggs' ],
				[ 'id' => 'Q10', 'label' => 'Joe Builder Inc.' ],
				[ 'id' => 'Q11', 'label' => 'Jolly Construction Ltd.' ]
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
		$presenter = new SpySearchEntitiesPresenter();
		$searcher = $this->newSearchEntitiesUseCase( $this->getPeopleSearchResults(), $presenter );
		$searcher->search( 'Jo' );

		$this->assertSame(
			[],
			$presenter->searchResult
		);
	}

	public function testResultsFoundWithFiltering(): void {
		$presenter = new SpySearchEntitiesPresenter();
		$searcher = $this->newSearchEntitiesUseCase( $this->getAllSearchResults(), $presenter );
		$searcher->search( 'jo' );

		$this->assertSame(
			[
				[ 'id' => 'Q10', 'label' => 'Joe Builder Inc.' ],
				[ 'id' => 'Q11', 'label' => 'Jolly Construction Ltd.' ]
			],
			$presenter->searchResult
		);
	}

}
