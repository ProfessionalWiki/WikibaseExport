<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application\Export;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\EntityMapperFactory;
use ProfessionalWiki\WikibaseExport\Application\EntitySourceFactory;
use ProfessionalWiki\WikibaseExport\Application\Export\ExportAuthorizer;
use ProfessionalWiki\WikibaseExport\Application\Export\ExportPresenter;
use ProfessionalWiki\WikibaseExport\Application\Export\ExportRequest;
use ProfessionalWiki\WikibaseExport\Application\Export\ExportUseCase;
use ProfessionalWiki\WikibaseExport\Application\TimeQualifierProperties;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\FailingExportAuthorizer;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\SpyExportPresenter;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\SucceedingExportAuthorizer;
use ProfessionalWiki\WikibaseExport\WikibaseExportExtension;
use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikibase\Repo\WikibaseRepo;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\Export\ExportUseCase
 */
class ExportUseCaseTest extends TestCase {

	private function newExportUseCase( ExportPresenter $presenter, ExportAuthorizer $authorizer ): ExportUseCase {
		return new ExportUseCase(
			ungroupedProperties: [],
			propertiesGroupedByYear: [],
			entitySourceFactory: new EntitySourceFactory(
				lookup: WikibaseRepo::getEntityLookup()
			),
			entityMapperFactory: new EntityMapperFactory(
				timeQualifierProperties: new TimeQualifierProperties(
					pointInTime: new NumericPropertyId( 'P1' ),
					startTime: new NumericPropertyId( 'P2' ),
					endTime: new NumericPropertyId( 'P3' ),
				),
				statementMapper: WikibaseExportExtension::getInstance()->newStatementMapper(),
				contentLanguage: 'en'
			),
			presenter: $presenter,
			authorizer: $authorizer
		);
	}

	public function testNothingIsPresented(): void {
		$presenter = new SpyExportPresenter();
		$useCase = $this->newExportUseCase( $presenter, new SucceedingExportAuthorizer() );

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
		$useCase = $this->newExportUseCase( $presenter, new SucceedingExportAuthorizer() );

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

	public function testPermissionDeniedIsPresented(): void {
		$presenter = new SpyExportPresenter();
		$useCase = $this->newExportUseCase( $presenter, new FailingExportAuthorizer() );

		$useCase->export(
			new ExportRequest(
				subjectIds: [],
				statementPropertyIds: [],
				startYear: 2020,
				endYear: 2022
			)
		);

		$this->assertTrue(
			$presenter->presentedPermissionDenied
		);
	}

}
