<?php
/**
 * @package ttf-one
 */

if ( ! function_exists( 'ttf_one_sanitize_text' ) ) :
/**
 * Sanitize a string to allow only tags in the allowedtags array.
 *
 * @since  1.0
 *
 * @param  string    $string    The unsanitized string.
 * @return string               The sanitized string.
 */
function ttf_one_sanitize_text( $string ) {
	global $allowedtags;
	return wp_kses( $string , $allowedtags );
}
endif;

if ( ! function_exists( 'ttf_one_sanitize_choice' ) ) :
/**
 * Sanitize a value from a list of allowed values.
 *
 * The first value in the 'allowed_choices' array will be the default if the given
 * value doesn't match anything in the array.
 *
 * @param mixed $value
 * @param mixed $setting
 *
 * @return mixed
 */
function ttf_one_sanitize_choice( $value, $setting ) {
	if ( is_object( $setting ) ) {
		$setting = $setting->id;
	}

	$allowed_choices = array( 0 );

	switch ( $setting ) {
		case 'site-layout' :
			$allowed_choices = array( 'full-width', 'boxed' );
			break;
		case 'background-size' :
			$allowed_choices = array( 'actual', 'cover', 'contain' );
			break;
		case 'background-repeat' :
			$allowed_choices = array( 'no-repeat', 'tile', 'tile-h', 'tile-v' );
			break;
		case 'background-position' :
			$allowed_choices = array( 'left', 'center', 'right' );
			break;
		case 'background-attachment' :
			$allowed_choices = array( 'fixed', 'scroll' );
			break;
		case 'font-site-title' || 'font-header' || 'font-body' :
			$fonts = ttf_one_get_google_fonts();
			$allowed_choices = array_keys( $fonts );
			break;
		case 'header-layout' || 'footer-layout' :
			$allowed_choices = array( 'layout-1', 'layout-2', 'layout-3', 'layout-4' );
			break;
	}

	if ( ! in_array( $value, $allowed_choices ) ) {
		$value = $allowed_choices[0];
	}

	return $value;
}
endif;

if ( ! class_exists( 'TTF_One_Prioritizer' ) ) :
/**
 * Class TTF_One_Prioritizer
 *
 * @since 1.0
 */
class TTF_One_Prioritizer {
	public $initial_priority = 0;
	public $increment = 0;
	public $current_priority = 0;

	function __construct( $initial_priority = 100, $increment = 100 ) {
		$this->initial_priority = absint( $initial_priority );
		$this->increment = absint( $increment );
		$this->current_priority = $this->initial_priority;
	}

	public function get() {
		return absint( $this->current_priority );
	}

	public function inc( $increment = 0 ) {
		if ( 0 === $increment ) {
			$increment = $this->increment;
		}
		$this->current_priority += absint( $increment );
	}

	public function add() {
		$priority = $this->get();
		$this->inc();
		return $priority;
	}

	public function reboot() {
		$this->current_priority = $this->initial_priority;
	}
}
endif;