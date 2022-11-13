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
		 * @param {string} value
		 * @param {string} label
		 * @param {boolean} selected
		 * @return {OO.ui.FieldLayout}
		 */
		createCheckbox: function ( value, label, selected = false ) {
			return new OO.ui.FieldLayout(
				new OO.ui.CheckboxInputWidget( {
					value: value,
					selected: selected
				} ),
				{
					label: label,
					align: 'inline'
				}
			);
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
			this.yearStart = new OO.ui.NumberInputWidget( {
				id: 'yearStart'
			} );

			this.yearEnd = new OO.ui.NumberInputWidget( {
				id: 'yearEnd'
			} );

			return this.createSection(
				'filters',
				mw.msg( 'wikibase-export-filters-heading' ),
				[
					new OO.ui.FieldLayout( this.yearStart, {
						label: mw.msg( 'wikibase-export-start-year' )
					} ),
					new OO.ui.FieldLayout( this.yearEnd, {
						label: mw.msg( 'wikibase-export-end-year' )
					} )
				]
			);
		},

		/**
		 * @return {OO.ui.PanelLayout}
		 */
		createStatementsSection: function () {
			const widget = this;

			const allStatementsLayout = this.createCheckbox( 'all', mw.msg( 'wikibase-export-statement-group-all' ), true );
			this.allStatements = allStatementsLayout.getField();

			this.statements = new OO.ui.MenuTagMultiselectWidget( {
				id: 'statements',
				inputPosition: 'outline',
				placeholder: mw.msg( 'wikibase-export-statements-placeholder' )
			} );
			this.statements.toggle( false );

			this.allStatements.on( 'change', function ( selected ) {
				if ( selected ) {
					widget.statements.toggle( false );
					widget.statements.setValue( widget.statements.menu.getItems() );
				} else {
					widget.statements.removeItems( widget.statements.getItems() );
					widget.statements.toggle( true );
				}
			} );

			this.addAllowedProperties();

			return this.createSection(
				'statements',
				mw.msg( 'wikibase-export-statements-heading' ),
				[ allStatementsLayout, this.statements ]
			);
		},

		addAllowedProperties: function () {
			const widget = this;

			new mw.Api().get( {
				action: 'wbgetentities',
				ids: mw.config.get( 'wgWikibaseExportProperties' )
			} ).then( function ( data ) {
				const options = widget.getPropertyOptionsFromData( data );
				widget.statements.addOptions( options );
				widget.statements.setValue( options );
			} );
		},

		/**
		 * @param {Object} data
		 * @return {{data: *, label: *}[]}
		 */
		getPropertyOptionsFromData: function ( data ) {
			return Object.keys( data.entities ).map(
				( key ) => this.getPropertyOptionFromEntity( data.entities[ key ] )
			);
		},

		/**
		 * @param {Object} entity
		 * @return {{data: string, label: string}}
		 */
		getPropertyOptionFromEntity: function ( entity ) {
			return {
				data: entity.id,
				// TODO: try current language, then fallback to default
				label: entity.labels.en.value || entity.id
			};
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

			const widget = this;

			submitButton.on( 'click', function () {
				window.location = widget.buildDownloadUrl();
			} );

			return submitButton;
		},

		/**
		 * @return {string}
		 */
		buildDownloadUrl: function () {
			return mw.util.wikiScript( 'rest' ) +
				'/wikibase-export/v0/export?' +
				this.getQueryParams().toString();
		},

		getQueryParams: function () {
			const subjectIds = this.subjects.getValue().join( '|' );
			const startTime = this.yearStart.getValue();
			const endTime = this.yearEnd.getValue();
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
