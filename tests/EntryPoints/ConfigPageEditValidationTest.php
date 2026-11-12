<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\EntryPoints;

use MediaWiki\Api\ApiUsageException;
use MediaWiki\Tests\Api\ApiTestCase;
use ProfessionalWiki\WikibaseExport\Tests\TestDoubles\Valid;

/**
 * Integration, for the edit API that runs MediaWiki's edit hooks, and the database that holds the edited pages.
 *
 * @covers \ProfessionalWiki\WikibaseExport\EntryPoints\MediaWikiHooks
 * @group Database
 */
class ConfigPageEditValidationTest extends ApiTestCase {

	private const CONFIG_PAGE = 'MediaWiki:WikibaseExport';
	private const INVALID_CONFIG = '{ "defaultStartYear": "not a year" }';

	public function testRefusesInvalidConfig(): void {
		$this->expectApiErrorCode( 'wikibase-export-config-invalid-errors' );

		$this->edit( self::CONFIG_PAGE, self::INVALID_CONFIG );
	}

	public function testRefusalNamesTheInvalidSetting(): void {
		$this->assertStringContainsString( 'defaultStartYear', $this->getRefusalText() );
	}

	public function testSavesValidConfig(): void {
		$this->assertSame(
			'Success',
			$this->edit( self::CONFIG_PAGE, Valid::configJson() )['edit']['result']
		);
	}

	public function testSavesOtherPagesWithoutValidatingThem(): void {
		$this->assertSame(
			'Success',
			$this->edit( 'WikibaseExport', self::INVALID_CONFIG )['edit']['result']
		);
	}

	private function edit( string $pageName, string $text ): array {
		return $this->doApiRequestWithToken( [
			'action' => 'edit',
			'title' => $pageName,
			'text' => $text,
		] )[0];
	}

	private function getRefusalText(): string {
		try {
			$this->edit( self::CONFIG_PAGE, self::INVALID_CONFIG );
		} catch ( ApiUsageException $exception ) {
			return $exception->getMessage();
		}

		$this->fail( 'The edit was saved' );
	}

}
