<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\EntryPoints;

use EditPage;
use OutputPage;
use ProfessionalWiki\WikibaseExport\Persistence\ConfigJsonValidator;
use ProfessionalWiki\WikibaseExport\Presentation\ConfigJsonErrorFormatter;
use ProfessionalWiki\WikibaseExport\Presentation\ExportConfigEditPageTextBuilder;
use ProfessionalWiki\WikibaseExport\WikibaseExportExtension;
use Skin;
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

			$textBuilder = new ExportConfigEditPageTextBuilder( $editPage->getContext() );
			$editPage->editFormTextTop = $textBuilder->createTopHtml();
			$editPage->editFormTextBottom = $textBuilder->createBottomHtml();
		}
	}

	public static function onEditFormPreloadText( string &$text, Title &$title ): void {
		if ( WikibaseExportExtension::getInstance()->isConfigTitle( $title ) ) {
			$text = trim( '
{
	"startTimePropertyId": null,
	"endTimePropertyId": null,
	"pointInTimePropertyId": null,
	"propertiesToGroupByYear": [
	],
	"ungroupedProperties": [
	],
	"defaultSubjects": [
	],
	"defaultStartYear": null,
	"defaultEndYear": null,
	"subjectFilterPropertyId": null,
	"subjectFilterPropertyValue": null,
	"exportLanguages": [
	]
}' );
		}
	}

	public static function onBeforePageDisplay( OutputPage $out, Skin $skin ): void {
		$title = $out->getTitle();

		if ( $title === null ) {
			return;
		}

		if ( WikibaseExportExtension::getInstance()->isConfigTitle( $title ) ) {
			$html = $out->getHTML();
			$out->clearHTML();
			$out->addHTML( self::getConfigPageHtml( $html ) );
		}
	}

	private static function getConfigPageHtml( string $html ): string {
		$jsonTablePosition = strpos( $html, '<table class="mw-json">' );

		if ( !$jsonTablePosition ) {
			return $html;
		}

		return substr( $html, $jsonTablePosition );
	}

}
