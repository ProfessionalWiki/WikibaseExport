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
			WikibaseExportExtension::exportApiFactory(),
			new RequestData( [
				'queryParams' => [
					'subject_ids' => 'Q1|Q2|Q3',
					'statement_property_ids' => 'P1|P2',
					'start_time' => '2021',
					'end_time' => '2022',
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
