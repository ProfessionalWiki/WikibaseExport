<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\EntryPoints;

use EditPage;
use ProfessionalWiki\WikibaseExport\Persistence\ConfigJsonValidator;
use ProfessionalWiki\WikibaseExport\Presentation\ConfigJsonErrorFormatter;
use ProfessionalWiki\WikibaseExport\WikibaseExportExtension;
use Title;

class MediaWikiHooks {

	public static function onContentHandlerDefaultModelFor( Title $title, ?string &$model ): void {
		if ( WikibaseExportExtension::getInstance()->isConfigTitle( $title ) ) {
			$model = 'json'; // CONTENT_MODEL_JSON (string to make Psalm happy)
		}
	}

	public static function onEditFilter( EditPage $editPage, ?string $text, ?string $section, string &$error ): void {
		$validator = ConfigJsonValidator::newInstance();

		if ( is_string( $text )
			&& WikibaseExportExtension::getInstance()->isConfigTitle( $editPage->getTitle() )
			&& !$validator->validate( $text )
		) {
			$errors = $validator->getErrors();
			$error = \Html::errorBox(
				wfMessage( 'wikibase-export-config-invalid', count( $errors ) )->escaped() .
				ConfigJsonErrorFormatter::format( $errors )
			);
		}
	}

	public static function onAlternateEdit( EditPage $editPage ): void {
		if ( WikibaseExportExtension::getInstance()->isConfigTitle( $editPage->getTitle() ) ) {
			$editPage->suppressIntro = true;
		}
	}

	public static function onEditFormPreloadText( string &$text, Title &$title ): void {
		if ( WikibaseExportExtension::getInstance()->isConfigTitle( $title ) ) {
			$text = trim( '
{
	"entityLabelLanguage": null,
	"chooseSubjectsLabel": null,
	"filterSubjectsLabel": null,
	"defaultSubjects": [
	],
	"defaultStartYear": null,
	"defaultEndYear": null,
	"startYearPropertyId": null,
	"endYearPropertyId": null,
	"pointInTimePropertyId": null,
	"properties": [
	],
	"introText": null
}' );
		}
	}

}
