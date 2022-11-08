/*
 * JavaScript for WikibaseExport
 */

$( function () {
	'use strict';

	function init() {
		var wikibaseExport = $( document.getElementById( 'wikibase-export' ) );

		/* Subjects */
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
			label: 'Choose companies',
			items: [
				subjects
			]
		} );

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

		/* Filters */
		var dateStart = new OO.ui.NumberInputWidget();

		var dateEnd = new OO.ui.NumberInputWidget();

		var filtersFieldset = new OO.ui.FieldsetLayout( {
			label: 'Choose time range',
			items: [
				new OO.ui.FieldLayout( dateStart, {
					label: 'Start date'
				} ),
				new OO.ui.FieldLayout( dateEnd, {
					label: 'End date'
				} ),
			]
		} );

		var filtersPanel = new OO.ui.PanelLayout( {
			expanded: false,
			framed: true,
			padded: true,
			$content: filtersFieldset.$element,
			classes: [
				'container'
			]
		} );

		wikibaseExport.append( filtersPanel.$element );

		/* Statements */
		var statements = new mw.widgets.EntitiesMultiselectWidget( {
			inputPosition: 'outline',
			placeholder: 'Search properties',
			// TODO: get default values somewhere
			selected: [],
			options: [],
			entityType: 'property'
		} );

		var statementsFieldset = new OO.ui.FieldsetLayout( {
			label: 'Choose variables',
			items: [
				statements
			]
		} );

		var statementsPanel = new OO.ui.PanelLayout( {
			expanded: false,
			framed: true,
			padded: true,
			$content: statementsFieldset.$element,
			classes: [
				'container'
			]
		} );

		wikibaseExport.append( statementsPanel.$element );

		/* Format */
		var formats = new OO.ui.RadioSelectWidget( {
			items: [
				new OO.ui.RadioOptionWidget( {
					data: 'csvwide',
					label: 'CSV (Wide)'
				} ),
				new OO.ui.RadioOptionWidget( {
					data: 'csvnarrow',
					label: 'CSV (Narrow)'
				} )
			],
		} );
		formats.selectItemByData( 'csvwide' );

		var formatsFieldset = new OO.ui.FieldsetLayout( {
			label: 'Choose export format',
			items: [
				formats
			]
		} );

		var formatsPanel = new OO.ui.PanelLayout( {
			expanded: false,
			framed: true,
			padded: true,
			$content: formatsFieldset.$element,
			classes: [
				'container'
			]
		} );

		wikibaseExport.append( formatsPanel.$element );


		/* Actions */
		var submitButton = new OO.ui.ButtonWidget({
			label: 'Download',
			flags: [
				'primary',
				'progressive'
			]
		} );

		submitButton.on( 'click', function () {
			const subjectIds = subjects.getValue().join( '|' );
			const startTime = dateStart.getValue();
			const endTime = dateEnd.getValue();
			const propertyIds = statements.getValue().join( '|' );
			const format = formats.findSelectedItem().data;

			// TODO: build the correct URL
			window.location = '/rest.php/wikibase-export/v0/export' +
				'?subject_ids=' + subjectIds +
				'&statement_property_ids=' + propertyIds +
				'&start_time=' + startTime +
				'&end_time=' + endTime +
				'&format=' + format;
		} );

		wikibaseExport.append( submitButton.$element );
	}

	init();

} );
