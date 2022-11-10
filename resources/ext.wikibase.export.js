/*
 * JavaScript for WikibaseExport
 */

$( function () {
	'use strict';

	mw.WikibaseExport = {
		init: function () {
			this.$wikibaseExport = $( document.getElementById( 'wikibase-export' ) );

			this.$wikibaseExport.append( this.createSubjectsSection().$element );
			this.$wikibaseExport.append( this.createFiltersSection().$element );
			this.$wikibaseExport.append( this.createStatementsSection().$element );
			this.$wikibaseExport.append( this.createFormatsSection().$element );
			this.$wikibaseExport.append( this.createActions().$element );
		},

		/**
		 * @param {string} idPrefix
		 * @param {string} label
		 * @param {OO.ui.Element[]} items
		 * @return {OO.ui.PanelLayout}
		 */
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

		/**
		 * @return {OO.ui.PanelLayout}
		 */
		createSubjectsSection: function () {
			this.subjects = new mw.widgets.EntitiesMultiselectWidget( {
				id: 'subjects',
				inputPosition: 'outline',
				placeholder: mw.msg( 'wikibase-export-subjects-placeholder' ),
				// TODO: get default values somewhere
				selected: [
					'Q100', 'Q200'
				],
				options: [
					{ data: 'Q100', label: 'Foo Bar' },
					{ data: 'Q200', label: 'Bar Baz' }
				]
			} );

			return this.createSection(
				'subjects',
				mw.msg( 'wikibase-export-subjects-heading' ),
				[ this.subjects ]
			);
		},

		/**
		 * @return {OO.ui.PanelLayout}
		 */
		createFiltersSection: function () {
			this.dateStart = new OO.ui.NumberInputWidget( {
				id: 'dateStart'
			} );

			this.dateEnd = new OO.ui.NumberInputWidget( {
				id: 'dateEnd'
			} );

			return this.createSection(
				'filters',
				mw.msg( 'wikibase-export-filters-heading' ),
				[
					new OO.ui.FieldLayout( this.dateStart, {
						label: mw.msg( 'wikibase-export-start-date' )
					} ),
					new OO.ui.FieldLayout( this.dateEnd, {
						label: mw.msg( 'wikibase-export-end-date' )
					} )
				]
			);
		},

		/**
		 * @return {OO.ui.PanelLayout}
		 */
		createStatementsSection: function () {
			this.statements = new mw.widgets.EntitiesMultiselectWidget( {
				id: 'statements',
				inputPosition: 'outline',
				placeholder: mw.msg( 'wikibase-export-statements-placeholder' ),
				// TODO: get default values somewhere
				selected: [],
				options: [],
				entityType: 'property'
			} );

			return this.createSection(
				'statements',
				mw.msg( 'wikibase-export-statements-heading' ),
				[ this.statements ]
			);
		},

		/**
		 * @return {OO.ui.PanelLayout}
		 */
		createFormatsSection: function () {
			this.formats = new OO.ui.RadioSelectWidget( {
				id: 'formats',
				items: [
					new OO.ui.RadioOptionWidget( {
						data: 'csvwide',
						label: mw.msg( 'wikibase-export-format-csv-wide' )
					} ),
					new OO.ui.RadioOptionWidget( {
						data: 'csvlong',
						label: mw.msg( 'wikibase-export-format-csv-long' )
					} )
				]
			} );
			this.formats.selectItemByData( 'csvwide' );

			return this.createSection(
				'formats',
				mw.msg( 'wikibase-export-formats-heading' ),
				[ this.formats ]
			);
		},

		/**
		 * @return {OO.ui.ButtonWidget}
		 */
		createActions: function () {
			const submitButton = new OO.ui.ButtonWidget( {
				id: 'download',
				label: mw.msg( 'wikibase-export-download' ),
				flags: [
					'primary',
					'progressive'
				]
			} );

			submitButton.on( 'click', this.onSubmit );

			return submitButton;
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
