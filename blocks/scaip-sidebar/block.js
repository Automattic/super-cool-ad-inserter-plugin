( function( wp ) {
	/**
	 * Registers a new block provided a unique name and an object defining its behavior.
	 * @see https://github.com/WordPress/gutenberg/tree/master/blocks#api
	 */
	var registerBlockType = wp.blocks.registerBlockType;
	/**
	 * Returns a new element of given type. Element is an abstraction layer atop React.
	 * @see https://github.com/WordPress/gutenberg/tree/master/element#element
	 */
	var el = wp.element.createElement;
	/**
	 * Retrieves the translation of text.
	 * @see https://github.com/WordPress/gutenberg/tree/master/i18n#api
	 */
	var __ = wp.i18n.__;
	/**
	 * Dropdown <select> element
	 * @link https://github.com/WordPress/gutenberg/tree/master/packages/components/src/select-control
	 */
	var SelectControl = wp.components.SelectControl;
	/**
	 * Sidebar controls for the block
	 * @link https://github.com/WordPress/gutenberg/tree/master/packages/editor/src/components/inspector-controls
	 */
	var InspectorControls = wp.editor.InspectorControls;
	/**
	 * Literally just for a fancy dashicon
	 * @see https://github.com/WordPress/gutenberg/blob/master/packages/components/src/dashicon/README.md
	 */
	var dashicon = wp.components.Dashicon;
	/**
	 * Every block starts by registering a new block type definition.
	 * @see https://wordpress.org/gutenberg/handbook/block-api/
	 */
	registerBlockType( 'super-cool-ad-inserter-plugin/scaip-sidebar', {
		title: __( 'Inserted Ad Position Sidebar' ),
		icon: 'welcome-widgets-menus',
		category: 'widgets',
		supports: {
			html: false,
			align: true,
			anchor: false,
			alignWide: true,
			customClassName: true,
			className: true,
			multiple: true,
		},
		attributes: {
			number: {
				type: 'string',
			},
		},

		/**
		 * The edit function describes the structure of your block in the context of the editor.
		 * This represents what the editor will render when the block is used.
		 * @see https://wordpress.org/gutenberg/handbook/block-edit-save/#edit
		 *
		 * @param {Object} [props] Properties passed from the editor.
		 * @return {Element}       Element to render.
		 */
		edit: function( props ) {
			if ( ! window.scaip ) {
				return "Something is wrong with the Super Cool Ad Inserter Plugin.";
			}

			options_array=[];
			for ( var i = 1; i <= window.scaip.repetitions; i++ ) {
				options_array.push( {
					label: i.toString(),
					value: i.toString()
				} );
			}

			return [
				el(
					'div',
					{
						className: props.className,
						align: props.align,
					},
					el(
						SelectControl,
						{
							label: [
								el(
									dashicon,
									{
										icon: 'welcome-widgets-menus'
									},
								),
								__( 'Inserted Ad Position:' )
							],
							options: options_array,
							value: props.attributes.number,
							onChange: function( value ) { props.setAttributes( { number: value } ); },
						}
					)
				),
				el( InspectorControls, {},
					el(
						SelectControl,
						{
							label: [
								__( 'Inserted Ad Position:' )
							],
							options: options_array,
							value: props.attributes.number,
							onChange: function( value ) { props.setAttributes( { number: value } ); },
							help: [
								__( 'Which Inserted Ad Position sidebar should be displayed in this area? ' ), // trailing space is important.
								el(
									'a',
									{
										href: 'https://github.com/INN/super-cool-ad-inserter-plugin/blob/master/docs/configuration.md'
									},
									'View the documentation.'
								),
							],
						}
					),
				),
				// @todo InspectorControls with a help thing pointing users to the widget settings.
			];
		},

		/**
		 * The save function defines the way in which the different attributes should be combined
		 * into the final markup, which is then serialized by Gutenberg into `post_content`.
		 * @see https://wordpress.org/gutenberg/handbook/block-edit-save/#save
		 *
		 * Because this is a dynamic block, there's nothing returned here.
		 * Yet, because this dynamic block draws from the attributes saved for its blockself
		 * in the post_content, we must return *something* in order for the attributes
		 * to be saved in the post.
		 *
		 * Thus, we return a Comment node via `new Comment`.
		 * @todo This doesn't work in IE, which is sad and deserves further work.
		 * @see https://developer.mozilla.org/en-US/docs/Web/API/Comment
		 *
		 * @return {Element}       Element to render.
		 */
		save: function( attributes ) {
			// Rendering in PHP
			return new Comment( attributes );
		}
	} );
} )(
	window.wp
);
