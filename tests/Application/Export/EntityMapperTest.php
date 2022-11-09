<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application\Export;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\Export\EntityMapper;
use ProfessionalWiki\WikibaseExport\Application\Export\MappedEntity;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\Export\EntityMapper
 * @covers \ProfessionalWiki\WikibaseExport\Application\Export\MappedEntity
 */
class EntityMapperTest extends TestCase {

	public function testEmptyItem(): void {
		$mapper = new EntityMapper(
			statementPropertyIds: [],
			startTime: new DateTimeImmutable(),
			endTime: new DateTimeImmutable()
		);

		$this->assertEquals(
			new MappedEntity(
				id: 'Q42'
			),
			$mapper->map(
				new Item( new ItemId( 'Q42' ) )
			)
		);
	}



}
