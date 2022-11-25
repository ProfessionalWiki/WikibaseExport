<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Presentation;

use IContextSource;

class ExportConfigEditPageTextBuilder {

	public function __construct(
		private IContextSource $context
	) {
	}

	public function createHtml(): string {
		$html = '<div class="wikibase-export-config-help">';

		$html .= '<h2>' . $this->context->msg( 'wikibase-export-config-help' ) . '</h2>';
		$html .= $this->creatVariablesSection();
		$html .= $this->createExampleSection();
		$html .= $this->createMessagesSection();

		$html .= '</div>';

		return $html;
	}

	private function creatVariablesSection(): string {
		$html = '<h3>' . $this->context->msg( 'wikibase-export-config-help-variables' ) . '</h3>';
		$html .= '
<table class="wikitable">
	<thead>
		<tr><th>Variable</th><th>Description</th><th>Example</th></tr>
	</thead>
	<tbdoy>
		<tr><td><code>defaultSubjects</code></td><td>A list of item IDs to be selected by default on the form.</td><td><code>[ "Q1", "Q2" ]</code></td></tr>
		<tr><td><code>defaultStartYear</code></td><td>The default start year on the form.</td><td><code>2010</code></td></tr>
		<tr><td><code>defaultEndYear</code></td><td>The default end year on the form.</td><td><code>2022</code></td></tr>
		<tr><td><code>startTimePropertyId</code></td><td>Property ID of the qualifier used for the start of a time range.</td><td><code>"P100"</code></td></tr>
		<tr><td><code>endTimePropertyId</code></td><td>Property ID of the qualifier used for the end of a time range.</td><td><code>"P200"</code></td></tr>
		<tr><td><code>pointInTimePropertyId</code></td><td>Property ID of the qualifier used for a specific point in time.</td><td><code>"P300"</code></td></tr>
		<tr><td><code>properties</code></td><td>A list of property IDs for statements that may be included in the export.</td><td><code>[ "P1", "P2" ]</code></td></tr>
	</tbdoy>
</table>
';

		return $html;
	}

	private function createMessagesSection(): string {
		$html = '<h3>' . $this->context->msg( 'wikibase-export-config-help-messages' ) . '</h3>';
		$html .= '<ul>';
		$html .= '<li>' . $this->context->msg( 'wikibase-export-config-help-intro' ) . '</li>';
		$html .= '<li>' . $this->context->msg( 'wikibase-export-config-help-subjects-heading' ) . '</li>';
		$html .= '<li>' . $this->context->msg( 'wikibase-export-config-help-subjects-placeholder' ) . '</li>';
		$html .= '</ul>';

		return $html;
	}

	private function createExampleSection(): string {
		$html = '<h3>' . $this->context->msg( 'wikibase-export-config-help-example' ) . '</h3>';
		$html .= '<pre>' . $this->getExampleContents() . '</pre>';

		return $html;
	}

	private function getExampleContents(): string {
		$example = file_get_contents( __DIR__ . '/../../example.json' );

		if ( !is_string( $example ) ) {
			return '';
		}

		return $example;
	}

}
