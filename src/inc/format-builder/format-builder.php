<?php
/**
 * @package Make
 */

if ( ! class_exists( 'TTFMAKE_Format_Builder' ) ) :
/**
 * Class TTFMAKE_Format_Builder
 *
 * TinyMCE plugin that adds a Format Builder dialog to the editor.
 *
 * @since 1.4.0.
 */
class TTFMAKE_Format_Builder {
	/**
	 * The one instance of TTFMAKE_TinyMCE_Buttons.
	 *
	 * @since 1.0.0.
	 *
	 * @var   TTFMAKE_TinyMCE_Buttons
	 */
	private static $instance;

	/**
	 * Instantiate or return the one TTFMAKE_TinyMCE_Buttons instance.
	 *
	 * @since  1.0.0.
	 *
	 * @return TTFMAKE_TinyMCE_Buttons
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	public function __construct() {}


	public function init() {
		if ( current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' ) ) {
			// Add plugin and button
			add_filter( 'mce_external_plugins', array( $this, 'register_plugin' ) );
			add_filter( 'mce_buttons', array( $this, 'register_button' ) );

			// Add translations for plugin
			add_filter( 'wp_mce_translation', array( $this, 'add_translations' ), 10, 2 );

			// Add styles for the plugin and button
			add_action( 'admin_print_styles-post.php', array( $this, 'print_styles' ) );
			add_action( 'admin_print_styles-post-new.php', array( $this, 'print_styles' ) );

			// Enqueue scripts for plugin functionality
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
	}


	public function register_plugin( $plugins ) {
		$plugins['ttfmake_format_builder'] = trailingslashit( get_template_directory_uri() ) . 'inc/format-builder/js/plugin.js';
		return $plugins;
	}


	public function register_button( $buttons ) {
		$buttons[] = 'ttfmake_format_builder';
		return $buttons;
	}


	public function add_translations( $translations ) {
		$format_builder_translations = array(
			'Format Builder' => __( 'Format Builder', 'make' ),
		);

		return array_merge( $translations, $format_builder_translations );
	}


	public function print_styles() { ?>
		<style type="text/css">
			body .mce-window .mce-container-body.mce-abs-layout {
				overflow-y: hidden;
			}
			i.mce-i-ttfmake-format-builder {
				font: normal 20px/1 'dashicons';
				padding: 0;
				vertical-align: top;
				speak: none;
				-webkit-font-smoothing: antialiased;
				-moz-osx-font-smoothing: grayscale;
				margin-left: -2px;
				padding-right: 2px;
			}
			i.mce-i-ttfmake-format-builder:before {
				content: '\f502';
			}
		</style>
	<?php }


	public function enqueue_scripts( $hook_suffix ) {
		if ( in_array( $hook_suffix, array( 'post.php', 'post-new.php' ) ) ) {
			$dependencies = array( 'backbone', 'underscore', 'jquery' );

			// Core
			wp_enqueue_script(
				'ttfmake-format-builder-core',
				trailingslashit( get_template_directory_uri() ) . 'inc/format-builder/js/core.js',
				$dependencies,
				TTFMAKE_VERSION
			);

			// Base model
			wp_enqueue_script(
				'ttfmake-format-builder-model-base',
				trailingslashit( get_template_directory_uri() ) . 'inc/format-builder/js/models/base.js',
				$dependencies,
				TTFMAKE_VERSION
			);
			$dependencies[] = 'ttfmake-format-builder-model-base';

			// Format models
			$models = array(
				'button',
			);
			foreach ( $models as $model ) {
				$handle = 'ttfmake-format-builder-model-' . $model;
				wp_enqueue_script(
					$handle,
					trailingslashit( get_template_directory_uri() ) . "inc/format-builder/js/models/$model.js",
					$dependencies,
					TTFMAKE_VERSION
				);
				$dependencies[] = $handle;
			}
		}
	}
}
endif;

/**
 * Instantiate or return the one TTFMAKE_Format_Builder instance.
 *
 * @since  1.4.0.
 *
 * @return TTFMAKE_Format_Builder
 */
function ttfmake_format_builder() {
	return TTFMAKE_Format_Builder::instance();
}

/**
 * Run the init function for the Format Builder
 *
 * @since 1.4.0.
 *
 * @return void
 */
function ttfmake_format_builder_init() {
	ttfmake_format_builder()->init();
}

add_action( 'admin_init', 'ttfmake_format_builder_init' );