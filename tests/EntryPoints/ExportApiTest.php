<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\EntryPoints;

use MediaWiki\Rest\HttpException;
use MediaWiki\Rest\RequestData;
use MediaWiki\Tests\Rest\Handler\HandlerTestTrait;
use ProfessionalWiki\WikibaseExport\Tests\WikibaseExportIntegrationTest;
use ProfessionalWiki\WikibaseExport\WikibaseExportExtension;

/**
 * @covers \ProfessionalWiki\WikibaseExport\EntryPoints\ExportApi
 * @group Database
 */
class ExportApiTest extends WikibaseExportIntegrationTest {
	use HandlerTestTrait;

	public function testHappyPathStub(): void {
		$this->editConfigPage( '
{
    "startTimePropertyId": "P100",
    "endTimePropertyId": "P200",
    "pointInTimePropertyId": "P300"
}
' );

		$response = $this->executeHandler(
			WikibaseExportExtension::exportApiFactory(),
			new RequestData( [
				'queryParams' => [
					'subject_ids' => 'Q1|Q2|Q3',
					'statement_property_ids' => 'P1|P2',
					'start_year' => 2021,
					'end_year' => 2022,
					'format' => 'csvwide'
				]
			] )
		);

		$this->assertSame( 200, $response->getStatusCode() );
		$this->assertSame( 'attachment; filename=export.csv;', $response->getHeaderLine( 'Content-Disposition' ) );

		// TODO: setup fake data and test it is included
		$this->assertSame(
			<<<CSV
CSV
,
			$response->getBody()->getContents()
		);
	}

	public function testInvalidRequestThrowsException(): void {
		$this->expectException( HttpException::class );

		$this->executeHandler(
			WikibaseExportExtension::exportApiFactory(),
			new RequestData( [
				'queryParams' => [
					'subject_ids' => 'Q1|Q2|Q3',
					'statement_property_ids' => 'P1|P2',
					'start_year' => 2022,
					'end_year' => 2020,
					'format' => 'csvwide'
				]
			] )
		);
	}

}
