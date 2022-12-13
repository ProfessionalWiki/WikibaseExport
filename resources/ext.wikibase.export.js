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

			const items = [];

			if ( this.config.showExportLanguages ) {
				items.push( this.createLanguageSection() );
			}

			items.push( this.createSubjectsSection() );

			if ( this.config.showPropertiesGroupedByYear ) {
				items.push( this.createGroupedStatementsSection() );
			}

			if ( this.config.showUngroupedProperties ) {
				items.push( this.createUngroupedStatementsSection() );
			}

			if ( !this.config.showPropertiesGroupedByYear && !this.config.showUngroupedProperties ) {
				items.push( this.createIncompleteConfigSection() );
			}

			if ( this.config.showPropertiesGroupedByUear || this.config.showUngroupedProperties ) {
				items.push( this.createConfigSection() );
			}

			items.push( this.createActions() );

			this.form.addItems( items );

			this.addAllowedProperties();

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
				$content: fieldset.$element
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
		createLanguageSection: function () {
			this.language = new OO.ui.DropdownInputWidget( {
				id: 'language',
				options: Object.keys( this.config.exportLanguages ).map(
					( languageCode ) => ( {
						data: languageCode,
						label: this.config.exportLanguages[ languageCode ]
					} )
				)
			} );

			this.language.on( 'change', () => {
				this.subjects.language = this.language.getValue();
				this.subjects.requestCache = {};
			} );

			this.language.setValue( Object.keys( this.config.exportLanguages )[ 0 ] );

			return this.createSection(
				'language',
				mw.msg( 'wikibase-export-language-heading' ),
				[ this.language ]
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
				language: this.getLanguage()
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
				languages: this.getLanguage(),
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
		createGroupedStatementsSection: function () {
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

			const allStatementsLayout = this.createCheckbox( 'all', mw.msg( 'wikibase-export-statement-group-all' ), true );
			this.allGroupedStatements = allStatementsLayout.getField();

			this.groupedStatements = new OO.ui.MenuTagMultiselectWidget( {
				id: 'grouped-statements',
				inputPosition: 'outline',
				placeholder: mw.msg( 'wikibase-export-statements-placeholder' )
			} );
			this.groupedStatements.toggle( false );

			this.allGroupedStatements.on( 'change', function ( selected ) {
				if ( selected ) {
					widget.groupedStatements.toggle( false );
					widget.groupedStatements.setValue( widget.groupedStatements.menu.getItems() );
				} else {
					widget.groupedStatements.removeItems( widget.groupedStatements.getItems() );
					widget.groupedStatements.toggle( true );
				}
			} );

			return this.createSection(
				'grouped-statements',
				mw.msg( 'wikibase-export-grouped-statements-heading' ),
				[
					new OO.ui.FieldLayout( this.startYear, {
						label: mw.msg( 'wikibase-export-start-year' )
					} ),
					new OO.ui.FieldLayout( this.endYear, {
						label: mw.msg( 'wikibase-export-end-year' )
					} ),
					allStatementsLayout,
					this.groupedStatements
				]
			);
		},

		/**
		 * @return {OO.ui.PanelLayout}
		 */
		createUngroupedStatementsSection: function () {
			const widget = this;

			const allStatementsLayout = this.createCheckbox( 'all', mw.msg( 'wikibase-export-statement-group-all' ), true );
			this.allUngroupedStatements = allStatementsLayout.getField();

			this.ungroupedStatements = new OO.ui.MenuTagMultiselectWidget( {
				id: 'ungrouped-statements',
				inputPosition: 'outline',
				placeholder: mw.msg( 'wikibase-export-statements-placeholder' )
			} );
			this.ungroupedStatements.toggle( false );

			this.allUngroupedStatements.on( 'change', function ( selected ) {
				if ( selected ) {
					widget.ungroupedStatements.toggle( false );
					widget.ungroupedStatements.setValue( widget.ungroupedStatements.menu.getItems() );
				} else {
					widget.ungroupedStatements.removeItems( widget.ungroupedStatements.getItems() );
					widget.ungroupedStatements.toggle( true );
				}
			} );

			return this.createSection(
				'ungrouped-statements',
				mw.msg( 'wikibase-export-ungrouped-statements-heading' ),
				[ allStatementsLayout, this.ungroupedStatements ]
			);
		},

		/**
		 * @return {OO.ui.PanelLayout}
		 */
		createIncompleteConfigSection: function () {
			let html = mw.msg( 'wikibase-export-config-incomplete' );

			if ( this.config.showConfigLink ) {
				html += '<br/>' + mw.message( 'wikibase-export-config-incomplete-link' ).parse();
			}

			const message = new OO.ui.HtmlSnippet( html );

			return this.createSection(
				'incomplete-config',
				'',
				[
					new OO.ui.Element( {
						content: [ message ]
					} )
				]
			);
		},

		/**
		 * @return {OO.ui.PanelLayout}
		 */
		createConfigSection: function () {
			self.headerType = new OO.ui.RadioSelectInputWidget( {
				options: [
					{ data: 'id', label: mw.msg( 'wikibase-export-config-header-id' ) },
					{ data: 'label', label: mw.msg( 'wikibase-export-config-header-label' ) }
				]
			} );

			return this.createSection(
				'export-config',
				mw.msg( 'wikibase-export-config-heading' ),
				[ self.headerType ]
			);
		},

		addAllowedProperties: function () {
			const widget = this;
			const ids = widget.config.groupedProperties.concat( widget.config.ungroupedProperties );

			if ( ids.length === 0 ) {
				return;
			}

			new mw.Api().get( {
				action: 'wbgetentities',
				ids: ids,
				languages: this.getLanguage(),
				languagefallback: true
			} ).then( function ( data ) {
				const options = widget.getOptionsFromEntityData( data );

				if ( widget.config.showPropertiesGroupedByYear ) {
					const groupedOptions = options.filter(
						( option ) => widget.config.groupedProperties.indexOf( option.data ) >= 0
					);

					widget.groupedStatements.addOptions( groupedOptions );
					widget.groupedStatements.setValue( groupedOptions );
				}

				if ( widget.config.showUngroupedProperties ) {
					const ungroupedOptions = options.filter(
						( option ) => widget.config.ungroupedProperties.indexOf( option.data ) >= 0
					);

					widget.ungroupedStatements.addOptions( ungroupedOptions );
					widget.ungroupedStatements.setValue( ungroupedOptions );
				}
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

			if ( entity.labels[ this.getLanguage() ] !== undefined ) {
				label = entity.labels[ this.getLanguage() ].value;
			}

			label += ' (' + entity.id + ') ';

			return {
				data: entity.id,
				label: label.trim()
			};
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

			/* eslint-disable camelcase */
			const params = {
				subject_ids: subjectIds
			};

			if ( this.config.showPropertiesGroupedByYear ) {
				params.start_year = this.startYear.getValue();
				params.end_year = this.endYear.getValue();
				params.grouped_statement_property_ids = this.groupedStatements.getValue().join( '|' );
			}

			if ( this.config.showUngroupedProperties ) {
				params.ungrouped_statement_property_ids = this.ungroupedStatements.getValue().join( '|' );
			}

			if ( this.config.showPropertiesGroupedByYear || this.config.showUngroupedProperties ) {
				params.header_type = self.headerType.getValue();
			}

			params.language = this.getLanguage();
			/* eslint-enable camelcase */

			return new URLSearchParams( params );
		},

		getLanguage: function () {
			if ( this.config.showExportLanguages ) {
				return this.language.getValue();
			}

			return Object.keys( this.config.exportLanguages )[ 0 ];
		}
	};

	mw.WikibaseExport.init();

} );
