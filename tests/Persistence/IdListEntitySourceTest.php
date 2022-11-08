<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Persistence;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Persistence\IdListEntitySource;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Services\Lookup\InMemoryEntityLookup;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Persistence\IdListEntitySource
 */
class IdListEntitySourceTest extends TestCase {

	public function testWhenThereAreNoIds_nullIsReturnedRightAway(): void {
		$entitySource = new IdListEntitySource(
			new InMemoryEntityLookup(),
			[]
		);

		$this->assertNull( $entitySource->next() );
	}

	public function testReturnsEntitiesAndThenNull(): void {
		$entitySource = new IdListEntitySource(
			new InMemoryEntityLookup(
				new Item( new ItemId( 'Q1' ) ),
				new Item( new ItemId( 'Q2' ) ),
			),
			[
				new ItemId( 'Q1' ),
				new ItemId( 'Q2' )
			]
		);

		$this->assertSame( 'Q1', (string)$entitySource->next()->getId() );
		$this->assertSame( 'Q2', (string)$entitySource->next()->getId() );
		$this->assertNull( $entitySource->next() );
	}

	public function testSkipsOverMissingEntities(): void {
		$entitySource = new IdListEntitySource(
			new InMemoryEntityLookup(
				new Item( new ItemId( 'Q2' ) ),
				new Item( new ItemId( 'Q4' ) ),
			),
			[
				new ItemId( 'Q1' ),
				new ItemId( 'Q2' ),
				new ItemId( 'Q3' ),
				new ItemId( 'Q4' ),
				new ItemId( 'Q5' ),
			]
		);

		$this->assertSame( 'Q2', (string)$entitySource->next()->getId() );
		$this->assertSame( 'Q4', (string)$entitySource->next()->getId() );
		$this->assertNull( $entitySource->next() );
	}

}
