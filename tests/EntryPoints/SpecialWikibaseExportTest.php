<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Tests\EntryPoints;

use MediaWiki\Permissions\Authority;
use PermissionsError;
use ProfessionalWiki\WikibaseExport\EntryPoints\SpecialWikibaseExport;
use SpecialPageTestBase;

/**
 * Integration, for MediaWiki's permission check on the special page, and the database that holds the test users.
 *
 * @covers \ProfessionalWiki\WikibaseExport\EntryPoints\SpecialWikibaseExport
 * @group Database
 */
class SpecialWikibaseExportTest extends SpecialPageTestBase {

	protected function newSpecialPage(): SpecialWikibaseExport {
		return new SpecialWikibaseExport();
	}

	public function testShowsTheExportPageToAUserWhoMayRead(): void {
		/** @var string $output */
		[ $output ] = $this->executeSpecialPage( '', null, null, $this->userWithRights( [ 'read' ] ) );

		$this->assertStringContainsString( 'id="wikibase-export"', $output );
	}

	public function testRefusesAUserWhoMayNotRead(): void {
		$user = $this->userWithRights( [] );

		$this->expectException( PermissionsError::class );
		$this->executeSpecialPage( '', null, null, $user );
	}

	/**
	 * @param string[] $rights
	 */
	private function userWithRights( array $rights ): Authority {
		$user = $this->getTestUser()->getUser();
		$this->overrideUserPermissions( $user, $rights );

		return $user;
	}

}
