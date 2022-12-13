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

	public function createTopHtml(): string {
		return '<div id="wikibase-export-config-help-top">' .
			$this->createMessagesSection() .
			$this->createDocumentationLink() .
			'</div>';
	}

	public function createBottomHtml(): string {
		return '<div id="wikibase-export-config-help-bottom">' .
			'<h2>' . $this->context->msg( 'wikibase-export-config-help' )->escaped() . '</h2>' .
			$this->creatVariablesSection() .
			$this->createExampleSection() .
			'</div>';
	}

	private function createDocumentationLink(): string {
		return '<p><a href="#wikibase-export-config-help-bottom">' .
			$this->context->msg( 'wikibase-export-config-help-documentation' )->escaped() .
			'</a></p>';
	}

	private function creatVariablesSection(): string {
		return '<h3>' . $this->context->msg( 'wikibase-export-config-help-variables' )->escaped() . '</h3>' .
			'<table class="wikitable">' .
			'<thead><tr>' .
			'<th>' . $this->context->msg( 'wikibase-export-config-help-table-variable' )->escaped() . '</th>' .
			'<th>' . $this->context->msg( 'wikibase-export-config-help-table-description' )->escaped() . '</th>' .
			'<th>' . $this->context->msg( 'wikibase-export-config-help-table-required' )->escaped() . '</th>' .
			'<th>' . $this->context->msg( 'wikibase-export-config-help-table-example' )->escaped() . '</th>' .
			'</tr></thead>' .
			'<tbody>' .
			$this->createTableRow(
				'startTimePropertyId',
				'wikibase-export-config-help-variable-start-time-property-id',
				'"P100"',
				true
			) .
			$this->createTableRow(
				'endTimePropertyId',
				'wikibase-export-config-help-variable-end-time-property-id',
				'"P200"',
				true
			) .
			$this->createTableRow(
				'pointInTimePropertyId',
				'wikibase-export-config-help-variable-point-in-time-property-id',
				'"P300"',
				true
			) .
			$this->createTableRow(
				'propertiesToGroupByYear',
				'wikibase-export-config-help-variable-properties-with-qualifiers',
				'[ "P1", "P2" ]',
				false
			) .
			$this->createTableRow(
				'ungroupedProperties',
				'wikibase-export-config-help-variable-properties-without-qualifiers',
				'[ "P3", "P4" ]',
				false
			) .
			$this->createTableRow(
				'defaultSubjects',
				'wikibase-export-config-help-variable-default-subjects',
				'[ "Q1", "Q2" ]',
				false
			) .
			$this->createTableRow(
				'defaultStartYear',
				'wikibase-export-config-help-variable-default-start-year',
				'2010',
				false
			) .
			$this->createTableRow(
				'defaultEndYear',
				'wikibase-export-config-help-variable-default-end-year',
				'2022',
				false
			) .
			$this->createTableRow(
				'subjectFilterPropertyId',
				'wikibase-export-config-help-variable-subject-filter-property-id',
				'"P50"',
				false
			) .
			$this->createTableRow(
				'subjectFilterPropertyValue',
				'wikibase-export-config-help-variable-subject-filter-property-value',
				'"company"',
				false
			) .
			$this->createTableRow(
				'exportLanguages',
				'wikibase-export-config-help-variable-export-languages',
				'[ "en", "nl" ]',
				false
			) .
			'</tbody>' .
			'</table>';
	}

	private function createTableRow( string $variable, string $messagePart, string $example, bool $required ): string {
		return '<tr>' .
			'<td>' . Html::element( 'code', [], $variable ) . '</td>' .
			'<td>' . $this->context->msg( $messagePart )->escaped() . '</td>' .
			'<td>' . $this->context->msg( $this->getRequiredMessage( $required ) )->escaped() . '</td>' .
			'<td>' . Html::element( 'code', [], $example ) . '</td>' .
			'</tr>';
	}

	private function getRequiredMessage( bool $required ): string {
		return $required
			? 'wikibase-export-config-help-table-required-yes'
			: 'wikibase-export-config-help-table-required-no';
	}

	private function createMessagesSection(): string {
		return '<p>' . $this->context->msg( 'wikibase-export-config-help-messages' )->escaped() . '</p>' .
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
