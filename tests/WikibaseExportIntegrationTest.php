<?php

namespace ProfessionalWiki\WikibaseExport\Tests;

use MediaWikiIntegrationTestCase;
use ProfessionalWiki\WikibaseExport\WikibaseExportExtension;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikibase\DataModel\Entity\Property;
use Wikibase\DataModel\Term\Fingerprint;
use Wikibase\DataModel\Term\Term;
use Wikibase\DataModel\Term\TermList;
use Wikibase\Repo\WikibaseRepo;

class WikibaseExportIntegrationTest extends MediaWikiIntegrationTestCase {

	protected function setUp(): void {
		parent::setUp();
		WikibaseExportExtension::getInstance()->clearConfig();
	}

	protected function editConfigPage( string $config ): void {
		$this->editPage(
			'MediaWiki:' . WikibaseExportExtension::CONFIG_PAGE_TITLE,
			$config
		);
	}

	protected function saveEntity( EntityDocument $entity ): void {
		WikibaseRepo::getEntityStore()->saveEntity(
			entity: $entity,
			summary: __CLASS__,
			user: self::getTestSysop()->getUser()
		);
	}

	protected function saveProperty( string $pId, string $type, string $label ): void {
		$this->saveEntity(
			new Property(
				id: new NumericPropertyId( $pId ),
				fingerprint: new Fingerprint( labels: new TermList( [
					new Term( languageCode: 'en', text: $label )
				] ) ),
				dataTypeId: $type
			)
		);
	}

	protected function skipOnPhp81AndLater(): void {
		if ( version_compare( PHP_VERSION, '8.1.0' ) >= 0 ) {
			$this->markTestSkipped( 'Wikibase does not yet work with PHP 8.1' );
		}
	}

}
