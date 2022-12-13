<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application\Export;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\EntitySourceFactory;
use ProfessionalWiki\WikibaseExport\Application\Export\ExportAuthorizer;
use ProfessionalWiki\WikibaseExport\Application\Export\ExportPresenter;
use ProfessionalWiki\WikibaseExport\Application\Export\ExportRequest;
use ProfessionalWiki\WikibaseExport\Application\Export\ExportUseCase;
use ProfessionalWiki\WikibaseExport\Application\PropertyIdList;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\FailingExportAuthorizer;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\FakeValueSetCreator;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\SpyExportPresenter;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\SucceedingExportAuthorizer;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\TimeHelper;
use Wikibase\Repo\WikibaseRepo;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\Export\ExportUseCase
 */
class ExportUseCaseTest extends TestCase {

	private function newExportUseCase( ExportPresenter $presenter, ExportAuthorizer $authorizer ): ExportUseCase {
		return new ExportUseCase(
			ungroupedProperties: new PropertyIdList(),
			propertiesGroupedByYear: new PropertyIdList(),
			timeQualifierProperties: TimeHelper::newTimeQualifierProperties(),
			entitySourceFactory: new EntitySourceFactory(
				lookup: WikibaseRepo::getEntityLookup()
			),
			presenter: $presenter,
			authorizer: $authorizer,
			valueSetCreatorFactory: new FakeValueSetCreator(),
			termLookup: WikibaseRepo::getTermLookup()
		);
	}

	public function testNothingIsPresented(): void {
		$presenter = new SpyExportPresenter();
		$useCase = $this->newExportUseCase( $presenter, new SucceedingExportAuthorizer() );

		$useCase->export( $this->newValidRequest() );

		$this->assertCount(
			0,
			$presenter->presentedEntitiesById
		);
	}

	private function newValidRequest(): ExportRequest {
		return new ExportRequest(
			languageCode: 'en',
			subjectIds: [],
			useLabelsInHeaders: false,
			groupedStatementPropertyIds: new PropertyIdList(),
			ungroupedStatementPropertyIds: new PropertyIdList(),
			startYear: 2020,
			endYear: 2022
		);
	}

	public function testInvalidRequestIsPresented(): void {
		$presenter = new SpyExportPresenter();
		$useCase = $this->newExportUseCase( $presenter, new SucceedingExportAuthorizer() );

		$useCase->export(
			new ExportRequest(
				languageCode: 'en',
				subjectIds: [],
				useLabelsInHeaders: false,
				groupedStatementPropertyIds: new PropertyIdList(),
				ungroupedStatementPropertyIds: new PropertyIdList(),
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

		$useCase->export( $this->newValidRequest() );

		$this->assertTrue(
			$presenter->presentedPermissionDenied
		);
	}

}
