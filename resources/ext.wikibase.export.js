/*
 * JavaScript for WikibaseExport
 */

$( function () {
	'use strict';

	mw.WikibaseExport = {
		init: function () {
			this.$wikibaseExport = $( document.getElementById( 'wikibase-export' ) );

			this.createSubjectsSection();
			this.createFiltersSection();
			this.createStatementsSection();
			this.createFormatsSection();
			this.createActions();
		},

		createSection: function ( idPrefix, label, items ) {
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
		},

		createSubjectsSection: function () {
			this.subjects = new mw.widgets.EntitiesMultiselectWidget( {
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

			const subjectsSection = this.createSection(
				'subjects',
				'Choose companies',
				[ this.subjects ]
			);

			this.$wikibaseExport.append( subjectsSection.$element );
		},

		createFiltersSection: function () {
			this.dateStart = new OO.ui.NumberInputWidget( {
				id: 'dateStart'
			} );

			this.dateEnd = new OO.ui.NumberInputWidget( {
				id: 'dateEnd'
			} );

			const filtersSection = this.createSection(
				'filters',
				'Choose time range',
				[
					new OO.ui.FieldLayout( this.dateStart, {
						label: 'Start date'
					} ),
					new OO.ui.FieldLayout( this.dateEnd, {
						label: 'End date'
					} )
				]
			);

			this.$wikibaseExport.append( filtersSection.$element );
		},

		createStatementsSection: function () {
			this.statements = new mw.widgets.EntitiesMultiselectWidget( {
				id: 'statements',
				inputPosition: 'outline',
				placeholder: 'Search properties',
				// TODO: get default values somewhere
				selected: [],
				options: [],
				entityType: 'property'
			} );

			const statementsSection = this.createSection(
				'statements',
				'Choose variables',
				[ this.statements ]
			);

			this.$wikibaseExport.append( statementsSection.$element );
		},

		createFormatsSection: function () {
			this.formats = new OO.ui.RadioSelectWidget( {
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
			this.formats.selectItemByData( 'csvwide' );

			const formatsSection = this.createSection(
				'formats',
				'Choose export format',
				[ this.formats ]
			);

			this.$wikibaseExport.append( formatsSection.$element );
		},

		createActions: function () {
			const submitButton = new OO.ui.ButtonWidget( {
				id: 'download',
				label: 'Download',
				flags: [
					'primary',
					'progressive'
				]
			} );

			submitButton.on( 'click', this.onSubmit );

			this.$wikibaseExport.append( submitButton.$element );
		},

		onSubmit: function () {
			// TODO: build the correct URL
			window.location = '/rest.php/wikibase-export/v0/export?' + this.getQueryParams().toString();
		},

		getQueryParams: function () {
			const subjectIds = this.subjects.getValue().join( '|' );
			const startTime = this.dateStart.getValue();
			const endTime = this.dateEnd.getValue();
			const propertyIds = this.statements.getValue().join( '|' );
			const format = this.formats.findSelectedItem().data;

			/* eslint-disable camelcase */
			return new URLSearchParams( {
				subject_ids: subjectIds,
				statement_property_ids: propertyIds,
				start_time: startTime,
				end_time: endTime,
				format: format
			} );
			/* eslint-enable camelcase */
		}
	};

	mw.WikibaseExport.init();

} );
