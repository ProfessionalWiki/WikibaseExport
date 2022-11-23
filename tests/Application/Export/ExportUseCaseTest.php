<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application\Export;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\EntityMapperBuilder;
use ProfessionalWiki\WikibaseExport\Application\EntitySourceBuilder;
use ProfessionalWiki\WikibaseExport\Application\Export\ExportPresenter;
use ProfessionalWiki\WikibaseExport\Application\Export\ExportRequest;
use ProfessionalWiki\WikibaseExport\Application\Export\ExportUseCase;
use ProfessionalWiki\WikibaseExport\Application\TimeQualifierProperties;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\SpyExportPresenter;
use ProfessionalWiki\WikibaseExport\WikibaseExportExtension;
use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikibase\Repo\WikibaseRepo;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\Export\ExportUseCase
 */
class ExportUseCaseTest extends TestCase {

	private function newExportUseCase( ExportPresenter $presenter ): ExportUseCase {
		return new ExportUseCase(
			entitySourceBuilder: new EntitySourceBuilder(
				lookup: WikibaseRepo::getEntityLookup()
			),
			entityMapperBuilder: new EntityMapperBuilder(
				timeQualifierProperties: new TimeQualifierProperties(
					pointInTime: new NumericPropertyId( 'P1' ),
					startTime: new NumericPropertyId( 'P2' ),
					endTime: new NumericPropertyId( 'P3' ),
				),
				statementMapper: WikibaseExportExtension::getInstance()->newStatementMapper()
			),
			presenter: $presenter
		);
	}

	public function testNothingIsPresented(): void {
		$presenter = new SpyExportPresenter();
		$useCase = $this->newExportUseCase( $presenter );

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
		$useCase = $this->newExportUseCase( $presenter );

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
