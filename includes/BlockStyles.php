<?php
/**
 * Handle block styles enqueuing and inlining.
 *
 * @package aristath/q
 *
 * @since 1.0
 */

namespace QTheme;

/**
 * Handle blocks styles.
 */
class BlockStyles {

	/**
	 * An array of block styles already added.
	 *
	 * @static
	 * @access protected
	 * @since 1.0
	 * @var array
	 */
	protected static $blocks = [];

	/**
	 * The class constructor.
	 *
	 * @access public
	 * @since 1.0
	 */
	public function __construct() {
		add_filter( 'render_block', [ $this, 'render_block' ], 10, 2 );
	}

	/**
	 * Print inline styles for blocks that need it.
	 *
	 * @access public
	 * @since 1.0
	 * @param string $html  The block HTML contents.
	 * @param array  $block The block.
	 * @return string       Returns the HTML.
	 */
	public function render_block( $html, $block ) {
		global $wp_styles;
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return '';
		}

		if ( ! in_array( $block['blockName'], self::$blocks, true ) ) {
			$path   = get_theme_file_path( "styles/blocks/{$block['blockName']}.css" );
			$handle = 'wp-block-' . str_replace( 'core/', '', $block['blockName'] );
			if ( file_exists( $path ) ) {
				if ( function_exists( 'gutenberg_register_core_block_styles' ) && isset( $wp_styles->registered[ $handle ] ) ) {
					// If the function exists then we're loading the stylesheets separately and we need to append our styles to them.
					ob_start();
					include $path;
					wp_add_inline_style( $handle, ob_get_clean() );
				} else {
					// The function does not exist so we'll just add our styles inline.
					echo '<style>';
					include $path;
					echo '</style>';
				}
			}
			self::$blocks[] = $block['blockName'];
		}
		return $html;
	}
}
