<?php
/**
 * WP_Ajax_Helper utility
 *
 * @package PhilipNewcomer\WP_Ajax_Helper
 */

namespace PhilipNewcomer\WP_Ajax_Helper;

/**
 * Class Utility
 */
class Utility {

	/**
	 * Gets the AJAX payload from the POST request.
	 *
	 * @param string $handle The current handler's handle.
	 *
	 * @return mixed The Ajax payload.
	 */
	public static function get_ajax_payload( $handle = '' ) {
		$payload = null;

		if ( ! empty( $_POST['payload'] ) ) {
			$payload = $_POST['payload']; // Could be either a string or an array.
		}

		/**
		 * Filter the Ajax payload.
		 *
		 * @param mixed  $payload The ajax payload.
		 * @param string $handle  The current handler's handle.
		 */
		$payload = apply_filters( 'WP_Ajax_Helper\ajax_payload', $payload, $handle );

		return $payload;
	}

	/**
	 * Retrieves the response from the handle's callback function.
	 *
	 * @param string $handle        The current handler's handle.
	 * @param mixed  $callback      The callback function.
	 * @param array  $callback_args The arguments to be passed to the callback function.
	 *
	 * @return mixed The callback response.
	 */
	public static function get_callback_response( $handle, $callback, $callback_args ) {

		$ajax_payload = static::get_ajax_payload( $handle );

		$response = static::run_callback(
			$callback,
			array(
				$ajax_payload,
				$callback_args,
			)
		);

		/**
		 * Filter the callback response.
		 *
		 * @param mixed  $response      The callback response.
		 * @param string $handle        The current handle's name.
		 * @param array  $callback_args The arguments passed to the callback function.
		 * @param mixed  $ajax_payload  The Ajax payload.
		 */
		$response = apply_filters( 'WP_Ajax_Helper\callback_response', $response, $handle, $callback_args, $ajax_payload );

		return $response;
	}

	/**
	 * Executes the specified callback function, passing in with the supplied arguments.
	 *
	 * @param mixed $callback      The callback function to run.
	 * @param array $callback_args The arguments to be passed to the callback function.
	 *
	 * @return mixed The callback result, or null if the callback was not callable, or \Exception if the callback threw an exception.
	 */
	public static function run_callback( $callback = '', $callback_args = array() ) {

		if ( ! is_callable( $callback ) ) {
			return null;
		}

		try {
			$result = call_user_func_array( $callback, $callback_args );

		} catch ( \Exception $caught_exception ) {
			$result = $caught_exception;
		}

		return $result;
	}

	/**
	 * Sanitizes and validates a handle name.
	 *
	 * @param string $handle The provided handle name.
	 *
	 * @return string The sanitized handle name.
	 */
	public static function sanitize_handle( $handle = null ) {

		$handle = sanitize_key( $handle );
		$handle = str_replace( '-', '_', $handle );

		return $handle;
	}

	/**
	 * Checks whether a nonce is valid.
	 *
	 * @param string $nonce_name The nonce name.
	 *
	 * @return bool Whether the nonce was valid.
	 */
	public static function validate_nonce( $nonce_name ) {

		return check_ajax_referer( $nonce_name, 'nonce', false );
	}
}
