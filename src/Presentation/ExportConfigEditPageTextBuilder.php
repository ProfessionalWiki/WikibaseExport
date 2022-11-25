<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Presentation;

use Html;
use IContextSource;
use Title;

class ExportConfigEditPageTextBuilder {

	public function __construct(
		private IContextSource $context
	) {
	}

	public function createHtml(): string {
		return '<div class="wikibase-export-config-help">' .
			'<h2>' . $this->context->msg( 'wikibase-export-config-help' )->escaped() . '</h2>' .
			$this->creatVariablesSection() .
			$this->createExampleSection() .
			$this->createMessagesSection() .
			'</div>';
	}

	private function creatVariablesSection(): string {
		return '<h3>' . $this->context->msg( 'wikibase-export-config-help-variables' )->escaped() . '</h3>' .
			'<table class="wikitable">' .
			'<thead><tr>' .
			'<th>' . $this->context->msg( 'wikibase-export-config-help-table-variable' )->escaped() . '</th>' .
			'<th>' . $this->context->msg( 'wikibase-export-config-help-table-description' )->escaped() . '</th>' .
			'<th>' . $this->context->msg( 'wikibase-export-config-help-table-example' )->escaped() . '</th>' .
			'</tr></thead>' .
			'<tbody>' .
			$this->createTableRow(
				'defaultSubjects',
				'wikibase-export-config-help-variable-default-subjects',
				'[ "Q1", "Q2" ]'
			) .
			$this->createTableRow(
				'defaultStartYear',
				'wikibase-export-config-help-variable-default-start-year',
				'2010'
			) .
			$this->createTableRow(
				'defaultEndYear',
				'wikibase-export-config-help-variable-default-end-year',
				'2022'
			) .
			$this->createTableRow(
				'startTimePropertyId',
				'wikibase-export-config-help-variable-start-time-property-id',
				'"P100"'
			) .
			$this->createTableRow(
				'endTimePropertyId',
				'wikibase-export-config-help-variable-end-time-property-id',
				'"P200"'
			) .
			$this->createTableRow(
				'pointInTimePropertyId',
				'wikibase-export-config-help-variable-point-in-time-property-id',
				'"P300"'
			) .
			$this->createTableRow(
				'properties',
				'wikibase-export-config-help-variable-properties',
				'[ "P1", "P2" ]'
			) .
			'</tbody>' .
			'</table>';
	}

	private function createTableRow( string $variable, string $messagePart, string $example ): string {
		return '<tr>' .
			'<td><code>' . $variable . '</code></td>' .
			'<td>' . $this->context->msg( "$messagePart" )->escaped() . '</td>' .
			'<td><code>' . $example . '</code></td>' .
			'</tr>';
	}

	private function createMessagesSection(): string {
		return '<h3>' . $this->context->msg( 'wikibase-export-config-help-messages' )->escaped() . '</h3>' .
			'<ul>' .
			$this->createMessageLinkItem(
				'wikibase-export-intro',
				'wikibase-export-config-help-message-intro'
			) .
			$this->createMessageLinkItem(
				'wikibase-export-subjects-heading',
				'wikibase-export-config-help-message-subjects-heading'
			) .
			$this->createMessageLinkItem(
				'wikibase-export-subjects-placeholder',
				'wikibase-export-config-help-message-subjects-placeholder'
			) .
			'</ul>';
	}

	private function createMessageLinkItem( string $targetMessage, string $textMessage ): string {
		$title = Title::newFromText( "MediaWiki:$targetMessage" );

		if ( $title === null ) {
			return '';
		}

		return '<li><a href="' . $title->getLocalURL() . '">' .
			$this->context->msg( $textMessage )->escaped() .
			'</a></li>';
	}

	private function createExampleSection(): string {
		return '<h3>' . $this->context->msg( 'wikibase-export-config-help-example' )->escaped() . '</h3>' .
			Html::element( 'pre', [], $this->getExampleContents() );
	}

	private function getExampleContents(): string {
		$example = file_get_contents( __DIR__ . '/../../example.json' );

		if ( !is_string( $example ) ) {
			return '';
		}

		return $example;
	}

}
