/*
 * JavaScript for WikibaseExport
 */

$( function () {
	'use strict';

	mw.WikibaseExport = {
		init: function () {
			this.config = mw.config.get( 'wgWikibaseExport' );

			this.$wikibaseExport = $( document.getElementById( 'wikibase-export' ) );

			this.$wikibaseExport.append( this.createForm().$element );
		},

		/**
		 * @return {OO.ui.FormLayout}
		 */
		createForm: function () {
			this.form = new OO.ui.FormLayout( {
				id: 'wikibase-export-form',
				action: '',
				method: 'get'
			} );

			this.form.addItems( [
				this.createSubjectsSection(),
				this.createFiltersSection(),
				this.createStatementsSection(),
				this.createFormatsSection(),
				this.createActions()
			] );

			return this.form;
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
				language: mw.config.get( 'wgUserLanguage' )
			} );

			this.addDefaultSubjects();

			return this.createSection(
				'subjects',
				mw.msg( 'wikibase-export-subjects-heading' ),
				[ this.subjects ]
			);
		},

		addDefaultSubjects: function () {
			const widget = this;

			new mw.Api().get( {
				action: 'wbgetentities',
				ids: widget.config.defaultSubjects,
				languages: mw.config.get( 'wgUserLanguage' ),
				languagefallback: true
			} ).then( function ( data ) {
				const options = widget.getOptionsFromEntityData( data );
				widget.subjects.addOptions( options );
				widget.subjects.setValue( options );
			} );
		},

		/**
		 * @return {OO.ui.PanelLayout}
		 */
		createFiltersSection: function () {
			const widget = this;
			const yearDiff = 100;

			this.startYear = new OO.ui.NumberInputWidget( {
				id: 'startYear',
				value: this.config.defaultStartYear,
				required: true
			} );

			this.endYear = new OO.ui.NumberInputWidget( {
				id: 'endYear',
				value: this.config.defaultEndYear,
				min: this.config.defaultStartYear,
				max: this.config.defaultStartYear + yearDiff,
				required: true
			} );

			this.startYear.on( 'change', function () {
				const startYear = widget.startYear.getValue();
				if ( widget.startYear.validate( startYear ) ) {
					widget.endYear.setRange( startYear, parseInt( startYear ) + yearDiff );
				}
			} );

			return this.createSection(
				'filters',
				mw.msg( 'wikibase-export-filters-heading' ),
				[
					new OO.ui.FieldLayout( this.startYear, {
						label: mw.msg( 'wikibase-export-start-year' )
					} ),
					new OO.ui.FieldLayout( this.endYear, {
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
				ids: widget.config.properties,
				languages: mw.config.get( 'wgUserLanguage' ),
				languagefallback: true
			} ).then( function ( data ) {
				const options = widget.getOptionsFromEntityData( data );
				widget.statements.addOptions( options );
				widget.statements.setValue( options );
			} );
		},

		/**
		 * @param {Object} data
		 * @return {{data: *, label: *}[]}
		 */
		getOptionsFromEntityData: function ( data ) {
			return Object.keys( data.entities ).map(
				( key ) => this.getOptionFromEntity( data.entities[ key ] )
			);
		},

		/**
		 * @param {Object} entity
		 * @return {{data: string, label: string}}
		 */
		getOptionFromEntity: function ( entity ) {
			let label = '';

			const userLanguage = mw.config.get( 'wgUserLanguage' );

			if ( entity.labels[ userLanguage ] !== undefined ) {
				label = entity.labels[ userLanguage ].value;
			}

			label += ' (' + entity.id + ') ';

			return {
				data: entity.id,
				label: label.trim()
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
			const submitButton = new OO.ui.ButtonInputWidget( {
				id: 'download',
				label: mw.msg( 'wikibase-export-download' ),
				flags: [
					'primary',
					'progressive'
				]
			} );

			const widget = this;

			submitButton.on( 'click', function () {
				if ( widget.form.$element[ 0 ].reportValidity() ) {
					window.location = widget.buildDownloadUrl();
				}
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
			const startYear = this.startYear.getValue();
			const endYear = this.endYear.getValue();
			const propertyIds = this.statements.getValue().join( '|' );
			const format = this.formats.findSelectedItem().data;

			/* eslint-disable camelcase */
			return new URLSearchParams( {
				subject_ids: subjectIds,
				statement_property_ids: propertyIds,
				start_year: startYear,
				end_year: endYear,
				format: format
			} );
			/* eslint-enable camelcase */
		}
	};

	mw.WikibaseExport.init();

} );
