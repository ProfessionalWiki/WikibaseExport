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

		if ( 'name' in config ) {
			// Use this instead of <input type="hidden">, because hidden inputs do not have separate
			// 'value' and 'defaultValue' properties.
			this.$hiddenInput = $( '<textarea>' )
				.addClass( 'oo-ui-element-hidden' )
				.attr( 'name', config.name )
				.appendTo( this.$element );
			// Update with preset values
			// Set the default value (it might be different from just being empty)
			this.$hiddenInput.prop( 'defaultValue', this.getItems().map( function ( item ) {
				return item.getData();
			} ).join( '\n' ) );
			this.on( 'change', function ( items ) {
				this.$hiddenInput.val( items.map( function ( item ) {
					return item.getData();
				} ).join( '\n' ) );
				// Trigger a 'change' event as if a user edited the text
				// (it is not triggered when changing the value from JS code).
				this.$hiddenInput.trigger( 'change' );
			}.bind( this ) );
		}

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
		var widget = this;

		this.getRequestData()
			.then( function ( data ) {
				widget.menu.clearItems();
				widget.menu.addItems( widget.getOptionsFromData( data ) );
			} ).always( function() {
				mw.widgets.EntitiesMultiselectWidget.parent.prototype.onInputChange.call( widget );
			} );
	};

	mw.widgets.EntitiesMultiselectWidget.prototype.getRequestData = function () {
		return new mw.Api().get( {
			action: 'wbsearchentities',
			type: 'item',
			language: 'en',
			uselang: 'en',
			search: this.getQueryValue()
		} );
	}

	mw.widgets.EntitiesMultiselectWidget.prototype.getOptionsFromData = function ( data ) {
		var items = [];

		for ( var i = 0; i < data.search.length; i++ ) {
			items.push( new OO.ui.MenuOptionWidget( {
				data: data.search[ i ].id,
				label: data.search[ i ].label
			} ) );
		}

		return items;
	}

}() );
