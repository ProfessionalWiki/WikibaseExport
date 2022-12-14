<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application\Export;

use DataValues\StringValue;
use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\SearchEntities\SearchEntitiesPresenter;
use ProfessionalWiki\WikibaseExport\Application\SearchEntities\SearchEntitiesUseCase;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\StubEntitySearchHelper;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\SpySearchEntitiesPresenter;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\StubEntitySearchHelper39;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikibase\DataModel\Services\Lookup\InMemoryEntityLookup;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\DataModel\Statement\StatementList;
use Wikibase\DataModel\Term\Term;
use Wikibase\Lib\Interactors\TermSearchResult;
use Wikibase\Repo\Api\EntitySearchHelper;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\SearchEntities\SearchEntitiesUseCase
 */
class SearchEntitiesUseCaseTest extends TestCase {

	private const LANGUAGE = 'en';
	private const STRING_INSTANCE_OF_ID = 'P1';
	private const STRING_INSTANCE_OF_VALUE = 'company';
	private const ITEM_INSTANCE_OF_ID = 'P2';
	private const ITEM_INSTANCE_OF_VALUE = 'Q100';

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
			$this->createTermSearchResult( 'Q11', 'Jolly Construction Ltd.' ),
			$this->createTermSearchResult( 'Q20', 'Johnson Roofs Inc.' ),
			$this->createTermSearchResult( 'Q21', 'Johnson Windows Ltd.' )
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
						new NumericPropertyId( self::STRING_INSTANCE_OF_ID ),
						new StringValue( self::STRING_INSTANCE_OF_VALUE )
					)
				)
			)
		);
	}

	private function createCompanyInstanceOfItem( string $id ): Item {
		return new Item(
			id: new ItemId( $id ),
			statements: new StatementList(
				new Statement(
					mainSnak: new PropertyValueSnak(
						new NumericPropertyId( self::ITEM_INSTANCE_OF_ID ),
						new EntityIdValue( new ItemId( self::ITEM_INSTANCE_OF_VALUE ) )
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
			$this->createCompanyInstanceOfItem( 'Q20' ),
			$this->createCompanyInstanceOfItem( 'Q21' )
		];
	}

	private function newStubEntitySearchHelper( array $searchResults ): EntitySearchHelper {
		if ( version_compare( MW_VERSION, '1.39.0', '>=' ) ) {
			return new StubEntitySearchHelper39( ...$searchResults );
		} else {
			return new StubEntitySearchHelper( ...$searchResults );
		}
	}

	private function newSearchEntitiesUseCase(
		array $searchResults,
		SearchEntitiesPresenter $presenter,
		?string $propertyId = self::STRING_INSTANCE_OF_ID,
		?string $propertyValue = self::STRING_INSTANCE_OF_VALUE
	): SearchEntitiesUseCase {
		return new SearchEntitiesUseCase(
			subjectFilterPropertyId: $propertyId,
			subjectFilterPropertyValue: $propertyValue,
			entitySearchHelper: $this->newStubEntitySearchHelper( $searchResults ),
			entityLookup: new InMemoryEntityLookup( ...$this->getAllEntities() ),
			presenter: $presenter
		);
	}

	public function testResultsAreNotFilteredWhenPropertyIdIsNull(): void {
		$presenter = new SpySearchEntitiesPresenter();
		$searcher = $this->newSearchEntitiesUseCase( $this->getAllSearchResults(), $presenter, propertyId: null );
		$searcher->search( 'Jo', self::LANGUAGE );

		$this->assertSame(
			[
				[ 'id' => 'Q1', 'label' => 'John Doe' ],
				[ 'id' => 'Q2', 'label' => 'Joe Bloggs' ],
				[ 'id' => 'Q10', 'label' => 'Joe Builder Inc.' ],
				[ 'id' => 'Q11', 'label' => 'Jolly Construction Ltd.' ],
				[ 'id' => 'Q20', 'label' => 'Johnson Roofs Inc.' ],
				[ 'id' => 'Q21', 'label' => 'Johnson Windows Ltd.' ]
			],
			$presenter->searchResult
		);
	}

	public function testResultsAreNotFilteredWhenPropertyValueIsNull(): void {
		$presenter = new SpySearchEntitiesPresenter();
		$searcher = $this->newSearchEntitiesUseCase( $this->getAllSearchResults(), $presenter, propertyValue: null );
		$searcher->search( 'Jo', self::LANGUAGE );

		$this->assertSame(
			[
				[ 'id' => 'Q1', 'label' => 'John Doe' ],
				[ 'id' => 'Q2', 'label' => 'Joe Bloggs' ],
				[ 'id' => 'Q10', 'label' => 'Joe Builder Inc.' ],
				[ 'id' => 'Q11', 'label' => 'Jolly Construction Ltd.' ],
				[ 'id' => 'Q20', 'label' => 'Johnson Roofs Inc.' ],
				[ 'id' => 'Q21', 'label' => 'Johnson Windows Ltd.' ]
			],
			$presenter->searchResult
		);
	}

	public function testNoResultsFoundWithoutFiltering(): void {
		$presenter = new SpySearchEntitiesPresenter();
		$searcher = $this->newSearchEntitiesUseCase( [], $presenter, propertyId: null );
		$searcher->search( 'Nothing', self::LANGUAGE );

		$this->assertSame(
			[],
			$presenter->searchResult
		);
	}

	public function testNoResultsFoundWithFiltering(): void {
		$presenter = new SpySearchEntitiesPresenter();
		$searcher = $this->newSearchEntitiesUseCase( $this->getPeopleSearchResults(), $presenter );
		$searcher->search( 'Jo', self::LANGUAGE );

		$this->assertSame(
			[],
			$presenter->searchResult
		);
	}

	public function testResultsFoundWithFiltering(): void {
		$presenter = new SpySearchEntitiesPresenter();
		$searcher = $this->newSearchEntitiesUseCase( $this->getAllSearchResults(), $presenter );
		$searcher->search( 'Jo', self::LANGUAGE );

		$this->assertSame(
			[
				[ 'id' => 'Q10', 'label' => 'Joe Builder Inc.' ],
				[ 'id' => 'Q11', 'label' => 'Jolly Construction Ltd.' ]
			],
			$presenter->searchResult
		);
	}

	public function testResultsFoundWithFilteringWhenPropertyValueIsAnId(): void {
		$presenter = new SpySearchEntitiesPresenter();
		$searcher = $this->newSearchEntitiesUseCase(
			$this->getAllSearchResults(),
			$presenter,
			self::ITEM_INSTANCE_OF_ID,
			self::ITEM_INSTANCE_OF_VALUE
		);
		$searcher->search( 'Jo', self::LANGUAGE );

		$this->assertSame(
			[
				[ 'id' => 'Q20', 'label' => 'Johnson Roofs Inc.' ],
				[ 'id' => 'Q21', 'label' => 'Johnson Windows Ltd.' ]
			],
			$presenter->searchResult
		);
	}

}
