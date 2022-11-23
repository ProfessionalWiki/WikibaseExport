<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application\Export;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\Config;
use ProfessionalWiki\WikibaseExport\Application\EntityMapperBuilder;
use ProfessionalWiki\WikibaseExport\Application\EntitySourceBuilder;
use ProfessionalWiki\WikibaseExport\Application\Export\ExportPresenter;
use ProfessionalWiki\WikibaseExport\Application\Export\ExportRequest;
use ProfessionalWiki\WikibaseExport\Application\Export\ExportUseCase;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\SpyExportPresenter;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\StubConfigLookup;
use ProfessionalWiki\WikibaseExport\WikibaseExportExtension;
use Wikibase\Repo\WikibaseRepo;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\Export\ExportUseCase
 */
class ExportUserCaseTest extends TestCase {

	public function newExportUseCase( Config $wikiConfig, ExportPresenter $presenter ): ExportUseCase {
		return new ExportUseCase(
			entitySourceBuilder: new EntitySourceBuilder(
				lookup: WikibaseRepo::getEntityLookup()
			),
			entityMapperBuilder: new EntityMapperBuilder(
				timeQualifierProperties: WikibaseExportExtension::getInstance()->newTimeQualifierProperties(
					new StubConfigLookup( $wikiConfig )
				),
				statementMapper: WikibaseExportExtension::getInstance()->newStatementMapper()
			),
			presenter: $presenter
		);
	}

	public function testNothingIsPresented(): void {
		$presenter = new SpyExportPresenter();
		$useCase = $this->newExportUseCase(
			new Config(
				startTimePropertyId: 'P1',
				endTimePropertyId: 'P2',
				pointInTimePropertyId: 'P3'
			),
			$presenter
		);

		$useCase->export(
			new ExportRequest(
				subjectIds: [],
				statementPropertyIds: [],
				startYear: 2020,
				endYear: 2022
			)
		);

		$this->assertCount(
			0,
			$presenter->presentedEntitiesById
		);
	}

	public function testInvalidRequestIsPresented(): void {
		$presenter = new SpyExportPresenter();
		$useCase = WikibaseExportExtension::getInstance()->newExportUseCase( $presenter );

		$useCase->export(
			new ExportRequest(
				subjectIds: [],
				statementPropertyIds: [],
				startYear: 2022,
				endYear: 2020
			)
		);

		$this->assertTrue(
			$presenter->presentedInvalidRequest
		);
	}

}
