( function () {

	/**
	 * Creates an mw.widgets.EntitiesMultiselectWidget object
	 *
	 * @class
	 * @extends OO.ui.MenuTagMultiselectWidget
	 *
	 * @constructor
	 * @param {Object} [config] Configuration options
	 */
	mw.widgets.EntitiesMultiselectWidget = function MwWidgetsEntitiesMultiselectWidget( config ) {
		// Parent constructor
		mw.widgets.EntitiesMultiselectWidget.parent.call( this, $.extend( true,
			{
				clearInputOnChoose: false,
				inputPosition: 'inline',
				allowEditTags: false
			},
			config
		) );

		// Initialization
		this.$element
			.addClass( 'mw-widgets-EntitiesMultiselectWidget' );

		this.language = config.language || 'en';
		this.entityType = config.entityType || 'item';
	};

	/* Setup */

	OO.inheritClass( mw.widgets.EntitiesMultiselectWidget, OO.ui.MenuTagMultiselectWidget );

	/* Methods */

	mw.widgets.EntitiesMultiselectWidget.prototype.getQueryValue = function () {
		return this.input.getValue();
	};

	/**
	 * @inheritdoc OO.ui.MenuTagMultiselectWidget
	 */
	mw.widgets.EntitiesMultiselectWidget.prototype.onInputChange = function () {
		const widget = this;

		this.getRequestData()
			.then( function ( data ) {
				widget.menu.clearItems();
				widget.menu.addItems( widget.getOptionsFromData( data ) );
			} ).always( function () {
				mw.widgets.EntitiesMultiselectWidget.parent.prototype.onInputChange.call( widget );
			} );
	};

	mw.widgets.EntitiesMultiselectWidget.prototype.getRequestData = function () {
		return new mw.Api().get( {
			action: 'wbsearchentities',
			type: this.entityType,
			language: this.language,
			uselang: this.language,
			search: this.getQueryValue()
		} );
	};

	mw.widgets.EntitiesMultiselectWidget.prototype.getOptionsFromData = function ( data ) {
		return data.search.map(
			( entity ) => new OO.ui.MenuOptionWidget( { data: entity.id, label: entity.label } )
		);
	};

}() );
