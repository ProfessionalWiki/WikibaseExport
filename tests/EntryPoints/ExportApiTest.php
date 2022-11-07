<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\EntryPoints;

use MediaWiki\Rest\RequestData;
use MediaWiki\Tests\Rest\Handler\HandlerTestTrait;
use MediaWikiIntegrationTestCase;
use ProfessionalWiki\WikibaseExport\WikibaseExportExtension;

/**
 * @covers \ProfessionalWiki\WikibaseExport\EntryPoints\ExportApi
 * @group Database
 */
class ExportApiTest extends MediaWikiIntegrationTestCase {
	use HandlerTestTrait;

	public function testHappyPathStub(): void {
		$response = $this->executeHandler(
			WikibaseExportExtension::getExportApiFactory(),
			new RequestData( [
				'queryParams' => [
					'subjectIds' => 'Q1|Q2|Q3',
					'statementPropertyIds' => 'P1|P2',
					'startTime' => '2021',
					'endTime' => '2022',
					'format' => 'csvwide'
				]
			] )
		);

		$this->assertSame( 200, $response->getStatusCode() );
		$this->assertSame( 'attachment; filename=export.csv;', $response->getHeaderLine( 'Content-Disposition' ) );

		$this->assertSame(
			<<<CSV
foo,bar
123,456
CSV
,
			$response->getBody()->getContents()
		);
	}

}
