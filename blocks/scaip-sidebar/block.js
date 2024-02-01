( function( wp ) {
	/**
	 * Registers a new block provided a unique name and an object defining its behavior.
	 * @see https://github.com/WordPress/gutenberg/tree/trunk/blocks#api
	 */
	var registerBlockType = wp.blocks.registerBlockType;
	/**
	 * Returns a new element of given type. Element is an abstraction layer atop React.
	 * @see https://github.com/WordPress/gutenberg/tree/trunk/element#element
	 */
	var el = wp.element.createElement;
	/**
	 * Retrieves the translation of text.
	 * @see https://github.com/WordPress/gutenberg/tree/trunk/i18n#api
	 */
	var __ = wp.i18n.__;
	/**
	 * Placeholder element
	 * @link https://github.com/WordPress/gutenberg/tree/trunk/packages/components/src/placeholder
	 */
	var Placeholder = wp.components.Placeholder;
	/**
	 * Dropdown <select> element
	 * @link https://github.com/WordPress/gutenberg/tree/trunk/packages/components/src/select-control
	 */
	var SelectControl = wp.components.SelectControl;
	/**
	 * Notice element
	 * @link https://github.com/WordPress/gutenberg/tree/trunk/packages/components/src/notice
	 */
	var Notice = wp.components.Notice;
	/**
	 * External Link element
	 * @link https://github.com/WordPress/gutenberg/tree/trunk/packages/components/src/external-link
	 */
	var ExternalLink = wp.components.ExternalLink;
	/**
	 * Literally just for a fancy dashicon
	 * @see https://github.com/WordPress/gutenberg/blob/trunk/packages/components/src/dashicon/README.md
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
			multiple: true
		},
		attributes: {
			number: {
				type: 'string'
			}
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

			var options_array = [] ;

			options_array.push( {
				label: __( 'Pick one:' ),
				disabled: true,
				value: 0,
			} );

			for ( var i = 1; i <= window.scaip.repetitions; i += 1 ) {
				options_array.push( {
					label: i.toString(),
					key: i.toString(),
					value: i.toString()
				} );
			}

			if ( typeof props.attributes.number !== 'undefined' ) {
				var value = props.attributes.number.toString();
			} else {
				var value = 0;
			}

			var notices = [];

			if ( window.scaip.sidebar_disabled ) {
				notices.push(
					el(
						Notice,
						{
							status: "error",
							isDismissible: false,
							children: __( 'The block is disabled due to a custom method for ad positioning and rendering being used.' ),
						}
					)
				);
			}


			return [
				el(
					'div',
					{
						className: props.className,
						align: props.align
					},
					el(
						Placeholder,
						{
							icon: el(
								dashicon,
								{
									icon: 'welcome-widgets-menus'
								}
							),
							label: __( 'Inserted Ad Position Sidebar' ),
							notices: notices,
							instructions: ! notices.length ? [
								__( 'Which Inserted Ad Position sidebar should be displayed in this area? ' ), // trailing space is important.
								el(
									ExternalLink,
									{
										href: 'https://github.com/Automattic/super-cool-ad-inserter-plugin/blob/trunk/docs/configuration.md'
									},
									'View the documentation.'
								)
							] : null
						},
						! window.scaip.sidebar_disabled && el(
							SelectControl,
							{
								label: __( 'Inserted Ad Position:' ),
								labelPosition: 'top',
								hideLabelFromVision: true,
								options: options_array,
								value: value,
								onChange: function( value ) {
									if ( value > 0 ) {
										props.setAttributes( { number: value.toString() } );
									}
								}
							}
						)
					)
				)
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
		 * Can't use https://developer.mozilla.org/en-US/docs/Web/API/Comment/Comment because
		 * that constructor isn't supported in IE.
		 *
		 * Therefore, use https://developer.mozilla.org/en-US/docs/Web/API/Document/createComment because
		 * that particular function has been around since 2000: https://www.w3.org/TR/2000/WD-DOM-Level-1-20000929/level-one-core.html#ID-1334481328
		 *
		 * @return {Element}       Element to render.
		 */
		save: function( attributes ) {
			// Rendering in PHP
			return document.createComment( attributes );
		}
	} );
} )(
	window.wp
);
