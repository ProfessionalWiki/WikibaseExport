/*
 * JavaScript for WikibaseExport
 */

$( function () {
	'use strict';

	function createSection( idPrefix, label, items ) {
		const fieldset = new OO.ui.FieldsetLayout( {
			id: idPrefix + '-fieldset',
			label: label,
			items: items
		} );

		return new OO.ui.PanelLayout( {
			id: idPrefix + '-panel',
			expanded: false,
			framed: true,
			padded: true,
			$content: fieldset.$element,
			classes: [
				'container'
			]
		} );
	}

	function init() {
		const $wikibaseExport = $( document.getElementById( 'wikibase-export' ) );

		/* Subjects */
		const subjects = new mw.widgets.EntitiesMultiselectWidget( {
			id: 'subjects',
			inputPosition: 'outline',
			placeholder: 'Search companies',
			// TODO: get default values somewhere
			selected: [
				'Q100', 'Q200'
			],
			options: [
				{ data: 'Q100', label: 'Foo Bar' },
				{ data: 'Q200', label: 'Bar Baz' }
			]
		} );

		const subjectsSection = createSection(
			'subjects',
			'Choose companies',
			[ subjects ]
		);

		$wikibaseExport.append( subjectsSection.$element );

		/* Filters */
		const dateStart = new OO.ui.NumberInputWidget( {
			id: 'dateStart'
		} );

		const dateEnd = new OO.ui.NumberInputWidget( {
			id: 'dateEnd'
		} );

		const filtersSection = createSection(
			'filters',
			'Choose time range',
			[
				new OO.ui.FieldLayout( dateStart, {
					label: 'Start date'
				} ),
				new OO.ui.FieldLayout( dateEnd, {
					label: 'End date'
				} )
			]
		);

		$wikibaseExport.append( filtersSection.$element );

		/* Statements */
		const statements = new mw.widgets.EntitiesMultiselectWidget( {
			id: 'statements',
			inputPosition: 'outline',
			placeholder: 'Search properties',
			// TODO: get default values somewhere
			selected: [],
			options: [],
			entityType: 'property'
		} );

		const statementsSection = createSection(
			'statements',
			'Choose variables',
			[ statements ]
		);

		$wikibaseExport.append( statementsSection.$element );

		/* Format */
		const formats = new OO.ui.RadioSelectWidget( {
			id: 'formats',
			items: [
				new OO.ui.RadioOptionWidget( {
					data: 'csvwide',
					label: 'CSV (Wide)'
				} ),
				new OO.ui.RadioOptionWidget( {
					data: 'csvnarrow',
					label: 'CSV (Narrow)'
				} )
			]
		} );
		formats.selectItemByData( 'csvwide' );

		const formatsSection = createSection(
			'formats',
			'Choose export format',
			[ formats ]
		);

		$wikibaseExport.append( formatsSection.$element );

		/* Actions */
		const submitButton = new OO.ui.ButtonWidget( {
			id: 'download',
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

		$wikibaseExport.append( submitButton.$element );
	}

	init();

} );
