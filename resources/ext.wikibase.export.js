/*
 * JavaScript for WikibaseExport
 */

$( function () {
	'use strict';

	function init() {
		var wikibaseExport = $( document.getElementById( 'wikibase-export' ) );

		var subjects = new mw.widgets.EntitiesMultiselectWidget( {
			inputPosition: 'outline',
			placeholder: 'Search companies',
			// TODO: get default values somewhere
			selected: [
				'Q100', 'Q200'
			],
			options: [
				{ data: 'Q100', label: 'Foo Bar' },
				{ data: 'Q200', label: 'Bar Baz' },
			]
		} );

		var subjectsFieldset = new OO.ui.FieldsetLayout( {
			label: 'Choose companies'
		} );
		subjectsFieldset.addItems( [ subjects ] );

		var subjectsPanel = new OO.ui.PanelLayout( {
			expanded: false,
			framed: true,
			padded: true,
			$content: subjectsFieldset.$element,
			classes: [
				'container'
			]
		} );
		wikibaseExport.append( subjectsPanel.$element );

		var submitButton = new OO.ui.ButtonWidget({
			label: 'Download',
			flags: [
				'primary',
				'progressive'
			]
		} );

		submitButton.on( 'click', function () {
			const subjectIds = subjects.getValue().join( '|' );
			// TODO: build the correct URL
			window.location = '/rest.php/wikibase-export/v0/export?subject_ids=' + subjectIds + '&statement_property_ids=P1|P2&start_time=2021&end_time=2022=format=csvwide';
		} );

		wikibaseExport.append( submitButton.$element );
	}

	init();

} );
