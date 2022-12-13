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
				clearInputOnChoose: true,
				inputPosition: 'inline',
				allowEditTags: false,
				menu: {
					filterFromInput: false
				}
			},
			config
		) );

		// Mixin constructors
		OO.ui.mixin.RequestManager.call( this, config );
		OO.ui.mixin.PendingElement.call( this, $.extend( true, {}, config, {
			$pending: this.$handle
		} ) );

		// Initialization
		this.$element
			.addClass( 'mw-widgets-EntitiesMultiselectWidget' );

		this.language = config.language;
		this.entityType = config.entityType || 'item';
	};

	/* Setup */

	OO.inheritClass( mw.widgets.EntitiesMultiselectWidget, OO.ui.MenuTagMultiselectWidget );
	OO.mixinClass( mw.widgets.EntitiesMultiselectWidget, OO.ui.mixin.RequestManager );
	OO.mixinClass( mw.widgets.EntitiesMultiselectWidget, OO.ui.mixin.PendingElement );

	/* Methods */

	mw.widgets.EntitiesMultiselectWidget.prototype.getQueryValue = function () {
		return this.input.getValue();
	};

	/**
	 * @inheritdoc OO.ui.MenuTagMultiselectWidget
	 */
	mw.widgets.EntitiesMultiselectWidget.prototype.onInputChange = function () {
		const widget = this;

		if ( this.getQueryValue() === '' ) {
			return;
		}

		this.getRequestData()
			.then( function ( data ) {
				widget.menu.clearItems();
				widget.menu.addItems( widget.getOptionsFromData( data ) );
			} ).always( function () {
				mw.widgets.EntitiesMultiselectWidget.parent.prototype.onInputChange.call( widget );
			} );
	};

	mw.widgets.EntitiesMultiselectWidget.prototype.getOptionsFromData = function ( data ) {
		return data.map(
			( entity ) => new OO.ui.MenuOptionWidget( {
				data: entity.id,
				label: this.getEntityOptionLabel( entity )
			} )
		);
	};

	mw.widgets.EntitiesMultiselectWidget.prototype.getEntityOptionLabel = function ( entity ) {
		const id = '(' + entity.id + ')';

		if ( entity.label !== undefined ) {
			return entity.label + ' ' + id;
		}

		return id;
	};

	/**
	 * @inheritdoc OO.ui.mixin.RequestManager
	 */
	mw.widgets.EntitiesMultiselectWidget.prototype.getRequestQuery = function () {
		return this.getQueryValue();
	};

	/**
	 * @inheritdoc OO.ui.mixin.RequestManager
	 */
	mw.widgets.EntitiesMultiselectWidget.prototype.getRequest = function () {
		const promiseAbortObject = { abort: function () {
			// Do nothing. This is just so OOUI doesn't break due to abort being undefined.
		} };

		const req = new mw.Rest().get(
			'/wikibase-export/v0/search-entities',
			{
				search: this.getQueryValue(),
				language: this.language,
				uselang: this.language
			}
		);

		promiseAbortObject.abort = req.abort.bind( req );

		return req.promise( promiseAbortObject );
	};

	/**
	 * @inheritdoc OO.ui.mixin.RequestManager
	 */
	// eslint-disable-next-line max-len
	mw.widgets.EntitiesMultiselectWidget.prototype.getRequestCacheDataFromResponse = function ( response ) {
		return response.search || [];
	};

}() );
