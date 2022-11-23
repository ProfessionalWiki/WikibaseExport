<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\Application\Export;

use PHPUnit\Framework\TestCase;
use ProfessionalWiki\WikibaseExport\Application\Export\ExportRequest;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\SpyExportPresenter;
use ProfessionalWiki\WikibaseExport\WikibaseExportExtension;

/**
 * @covers \ProfessionalWiki\WikibaseExport\Application\Export\ExportUseCase
 */
class ExportUserCaseTest extends TestCase {

	public function testNothingIsPresented(): void {
		$presenter = new SpyExportPresenter();
		$useCase = WikibaseExportExtension::getInstance()->newExportUseCase( $presenter );

		$useCase->export(
			new ExportRequest(
				subjectIds: [],
				statementPropertyIds: [],
				startYear: 2020,
				endYear: 2022
			)
		);

		$this->assertSame(
			0,
			$presenter->presentedEntitiesCount()
		);
	}

}
