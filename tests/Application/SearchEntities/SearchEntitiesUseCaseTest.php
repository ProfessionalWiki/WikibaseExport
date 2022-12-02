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
			matchedTerm: new Term( self::LANGUAGE, 'Irrelevant' ),
			matchedTermType: 'match',
			entityId: new ItemId( $id ),
			displayLabel: new Term( self::LANGUAGE, $label )
		);
	}

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
		$searchResults = [
			$this->createTermSearchResult( 'Q1', 'John Doe' ),
			$this->createTermSearchResult( 'Q10', 'Joe Bloggs' )
		];

		$presenter = new SpySearchEntitiesPresenter();
		$searcher = $this->newSearchEntitiesUseCase( $searchResults, $presenter, propertyId: null );
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
		$searchResults = [
			$this->createTermSearchResult( 'Q1', 'John Doe' ),
			$this->createTermSearchResult( 'Q10', 'Joe Bloggs' )
		];

		$presenter = new SpySearchEntitiesPresenter();
		$searcher = $this->newSearchEntitiesUseCase( $searchResults, $presenter, propertyValue: null );
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
			$this->createTermSearchResult( 'Q1', 'John Doe' ),
			$this->createTermSearchResult( 'Q10', 'Joe Bloggs' )
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
		$searchResults = [
			$this->createTermSearchResult( 'Q3', 'Company Foo' ),
			$this->createTermSearchResult( 'Q5', 'Company Foo Bar' ),
		];

		$presenter = new SpySearchEntitiesPresenter();
		$searcher = $this->newSearchEntitiesUseCase( $searchResults, $presenter );
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
