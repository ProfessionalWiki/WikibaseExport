<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\WikibaseExport\Presentation;

use Html;
use IContextSource;
use Message;
use SpecialPage;
use Title;

class ExportConfigEditPageTextBuilder {

	public function __construct(
		private IContextSource $context
	) {
	}

	public function createTopHtml(): string {
		return '<div id="wikibase-export-config-help-top">' .
			$this->createDocumentationLink() .
			'</div>';
	}

	private function createDocumentationLink(): string {
		return '<p>'
			. $this->context->msg( 'wikibase-export-config-help-documentation' )->parse()
			. '</p>';
	}

	public function createBottomHtml(): string {
		return <<<HTML
<div id="Documentation">
	<section>
		<h2 id="ConfigurationDocumentation">{$this->context->msg( 'wikibase-export-config-help' )->escaped()}</h2>

		<p>
			Besides the configuration reference below, you can consult the Wikibase Export
			<a href="https://professional.wiki/en/extension/wikibase-export">usage documentation</a> and
			<a href="https://export.wikibase.wiki">demo wiki</a>.
		</p>
	</section>

	<section>
		<h2 id="ExportLanguage">Export language</h2>

		<p>
			By default the export happens in the wiki's main language.
		</p>

		<p>
			You can change the export language or let the user choose by specifying multiple available languages.
		</p>

		<p>
			Example configuration:
		</p>

		<pre>
{
	"exportLanguages": [
		"en",
		"nl",
		"de"
	]
}</pre>
	</section>

	<section>
		<h2 id="ExportSubjects">Export subjects</h2>

		<p>
			You can specify the subjects that should be selected for export by default.
		</p>

		<pre>
{
    "defaultSubjects": [
        "Q1",
        "Q2"
    ]
}</pre>

		<p>
			This can be any entities defined in this wiki.
		</p>

		<p>
			You can also limit which entities show up in the search results via
			<code>subjectFilterPropertyId</code> and <code>subjectFilterPropertyValue</code>. Example:
		</p>

		<pre>
{
    "subjectFilterPropertyId": "P1",
    "subjectFilterPropertyValue": "Q2"
}</pre>
	</section>

	<section>
		<h2 id="UngroupedValues">Exporting ungrouped values</h2>

		<p>
			To have the "ungrouped values" section show on the export page, configure at least one ungroued property.
		</p>

		<pre>
{
    "ungroupedProperties": [
        "P1",
        "P2"
    ]
}</pre>

		<p>
			Statements with ungrouped properties will have their values be included in the export without
			grouping or filtering.
		</p>
	</section>

	<section>
		<h2 id="GroupingByYear">Grouping values by year</h2>

		<p>
			Values can be filtered and grouped by year. You can configure which properties should have their
			values be grouped this way.
		</p>

		<pre>
{
    "propertiesToGroupByYear": [
        "P3",
        "P4"
    ]
}</pre>

		<p>
			For the "values grouped by year" section to show on the export page, you need to configure at least
			one property to be grouped by year and at least one time qualifier property:
		</p>

		<pre>
{
    "startTimePropertyId": "P10",
    "endTimePropertyId": "P11"
}</pre>

		<p>
			You can optionally configure the default start and end years to be shown on the export page:
		</p>

		<pre>
{
    "defaultStartYear": 2019,
    "defaultEndYear": 2023
}</pre>

		<p>
			The current year will be used if there is no configuration for the defaults.
		</p>
	</section>

	<section>
		<h2 id="FullExample">{$this->context->msg( 'wikibase-export-config-help-example' )->escaped()}</h2>

		<pre>{$this->getExampleContents()}</pre>
	</section>

	<section>
		<h2 id="MessageCustomization">{$this->context->msg( 'wikibase-export-config-help-message-customization' )->escaped()}</h2>

		<p>
			{$this->context->msg( 'wikibase-export-config-help-messages' )->escaped()}
		</p>

		<ul>
			<li>{$this->createMessageLink( 'wikibaseexport-summary' )}</li>
			<li>{$this->createMessageLink( 'wikibase-export-intro' )}</li>
			<li>{$this->createMessageLink( 'wikibase-export-download' )}</li>
			<li>{$this->createMessageLink( 'wikibase-export-intro-admin-notice' )}</li>
			<li>{$this->createMessageLink( 'wikibase-export-language-heading' )}</li>
			<li>{$this->createMessageLink( 'wikibase-export-subjects-heading' )}</li>
			<li>{$this->createMessageLink( 'wikibase-export-subjects-placeholder' )}</li>
			<li>{$this->createMessageLink( 'wikibase-export-grouped-statements-heading' )}</li>
			<li>{$this->createMessageLink( 'wikibase-export-start-year' )}</li>
			<li>{$this->createMessageLink( 'wikibase-export-end-year' )}</li>
			<li>{$this->createMessageLink( 'wikibase-export-statement-group-all' )}</li>
			<li>{$this->createMessageLink( 'wikibase-export-ungrouped-statements-heading' )}</li>
			<li>{$this->createMessageLink( 'wikibase-export-statement-group-all' )}</li>
			<li>{$this->createMessageLink( 'wikibase-export-config-heading' )}</li>
			<li>{$this->createMessageLink( 'wikibase-export-config-header-id' )}</li>
			<li>{$this->createMessageLink( 'wikibase-export-config-header-label' )}</li>
		</ul>

		<p>
			To see where each message is used, <a href="{$this->getQqxLink()}">view Special:WikibaseExport?uselang=qqx</a>.
		</p>
	</section>
</div>
HTML;
	}

	private function getExampleContents(): string {
		$example = file_get_contents( __DIR__ . '/../../example.json' );

		if ( !is_string( $example ) ) {
			return '';
		}

		return $example;
	}

	private function createMessageLink( string $targetMessage ): string {
		$title = Title::newFromText( "MediaWiki:$targetMessage" );

		if ( $title === null ) {
			return '';
		}

		return Html::element( 'a', [ 'href' => $title->getLocalURL() ], $targetMessage );
	}

	private function getQqxLink(): string {
		return SpecialPage::getTitleFor( 'WikibaseExport' )->getLocalURL( [ 'uselang' => 'qqx' ] );
	}

}
