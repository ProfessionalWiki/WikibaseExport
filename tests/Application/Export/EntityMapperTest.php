<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application\Export;

use DataValues\StringValue;
use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\Export\EntityMapper;
use ProfessionalWiki\WikibaseExport\Application\Export\StatementsMapper;
use ProfessionalWiki\WikibaseExport\Application\Export\ValueSet;
use ProfessionalWiki\WikibaseExport\Application\Export\ValueSetList;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\EntityHelper;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\FakeStatementsMapper;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\DataModel\Statement\StatementList;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\Export\EntityMapper
 * @covers \ProfessionalWiki\WikibaseExport\Application\Export\MappedEntity
 */
class EntityMapperTest extends TestCase {

	public function testMapsId(): void {
		$this->assertSame(
			'Q42',
			$this->newMapper()->map(
				new Item( new ItemId( 'Q42' ) )
			)->id
		);
	}

	public function testMapsLabel(): void {
		$this->assertSame(
			'Foo Bar',
			$this->newMapper()->map(
				new Item(
					id: new ItemId( 'Q42' ),
					fingerprint: EntityHelper::newLabelFingerprint( 'Foo Bar' )
				)
			)->label
		);
	}

	/**
	 * @param StatementsMapper[] $statementsMappers
	 */
	private function newMapper( array $statementsMappers = [] ): EntityMapper {
		return new EntityMapper(
			languageCode: 'en',
			statementsMappers: $statementsMappers
		);
	}

	public function testBuildsValueSets(): void {
		$mappedEntity = $this->newMapper(
			[
				new FakeStatementsMapper( 'Foo' ),
				new FakeStatementsMapper( 'Bar' ),
			]
		)->map(
			new Item(
				id: new ItemId( 'Q42' ),
				fingerprint: EntityHelper::newLabelFingerprint( 'Whatever' ),
				statements: new StatementList(
					new Statement( new PropertyValueSnak( new NumericPropertyId( 'P1' ), new StringValue( '1' ) ) ),
					new Statement( new PropertyValueSnak( new NumericPropertyId( 'P2' ), new StringValue( '2' ) ) ),
				)
			)
		);

		$this->assertEquals(
			new ValueSetList( [
				new ValueSet( [ 'Foo P1', 'Foo P2' ] ),
				new ValueSet( [ 'Bar P1', 'Bar P2' ] )
			] ),
			$mappedEntity->valueSetList
		);
	}

}
