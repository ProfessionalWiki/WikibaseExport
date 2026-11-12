<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\EntryPoints;

use MediaWiki\Content\Content;
use MediaWiki\Context\IContextSource;
use MediaWiki\EditPage\EditPage;
use MediaWiki\Message\Message;
use MediaWiki\Output\OutputPage;
use MediaWiki\Status\Status;
use MediaWiki\Title\Title;
use MediaWiki\User\User;
use ProfessionalWiki\WikibaseExport\Persistence\ConfigJsonValidator;
use ProfessionalWiki\WikibaseExport\Presentation\ConfigJsonErrorFormatter;
use ProfessionalWiki\WikibaseExport\Presentation\ExportConfigEditPageTextBuilder;
use ProfessionalWiki\WikibaseExport\WikibaseExportExtension;
use Skin;

class MediaWikiHooks {

	public static function onContentHandlerDefaultModelFor( Title $title, ?string &$model ): void {
		if ( WikibaseExportExtension::getInstance()->isConfigTitle( $title ) ) {
			$model = 'json'; // CONTENT_MODEL_JSON (string to make Psalm happy)
		}
	}

	public static function onEditFilterMergedContent(
		IContextSource $context,
		Content $content,
		Status $status,
		string $summary,
		User $user,
		bool $minorEdit
	): void {
		$title = $context->getTitle();

		if ( $title === null || !WikibaseExportExtension::getInstance()->isConfigTitle( $title ) ) {
			return;
		}

		$validator = ConfigJsonValidator::newInstance();

		if ( !$validator->validate( $content->serialize() ) ) {
			$errors = $validator->getErrors();
			$status->fatal(
				'wikibase-export-config-invalid-errors',
				count( $errors ),
				Message::rawParam( ConfigJsonErrorFormatter::format( $errors ) )
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
	"exportLanguages": [
	],
	"defaultSubjects": [
	],
	"subjectFilterPropertyId": null,
	"subjectFilterPropertyValue": null,
	"ungroupedProperties": [
	],
	"propertiesToGroupByYear": [
	],
	"startTimePropertyId": null,
	"endTimePropertyId": null,
	"pointInTimePropertyId": null,
	"defaultStartYear": null,
	"defaultEndYear": null
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
