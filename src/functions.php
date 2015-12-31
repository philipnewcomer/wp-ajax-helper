<?php
/**
 * WP_Ajax_Helper functions
 *
 * @package PhilipNewcomer\WP_Ajax_Helper
 */

use PhilipNewcomer\WP_Ajax_Helper as Ajax;

/**
 * Convenience function to allow easy library instantiation using the default classes.
 *
 * @return PhilipNewcomer\WP_Ajax_Helper\Handler
 */
function wp_ajax_helper() {

	$validations = new Ajax\Validations;

	$responder   = new Ajax\Responder;
	$validator   = new Ajax\Validator( $validations );
	$frontend    = Ajax\Frontend::get_instance();
	$utility     = new Ajax\Utility;

	$handler = new Ajax\Handler( $responder, $validator, $frontend, $utility );

	return $handler;
}
