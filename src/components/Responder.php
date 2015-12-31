<?php
/**
 * WP_Ajax_Helper responder
 *
 * @package PhilipNewcomer\WP_Ajax_Helper
 */

namespace PhilipNewcomer\WP_Ajax_Helper;

/**
 * Class Responder
 */
class Responder {

	/**
	 * The callback response.
	 *
	 * @var mixed
	 */
	protected $callback_response;

	/**
	 * The response type.
	 *
	 * @var string
	 */
	protected $response_type;

	/**
	 * Handles a response.
	 *
	 * @param mixed $callback_response The response from the callback function.
	 */
	public function handle_response( $callback_response ) {

		$this->callback_response = $callback_response;
		$this->response_type = $this->get_response_type();

		$this->send_response_headers();
		$this->send_response_body();
	}

	/**
	 * Determines the type of response to return.
	 *
	 * @return string The response type.
	 */
	protected function get_response_type() {

		if ( is_array( $this->callback_response ) ) {
			$response_type = 'json';

		} elseif ( is_string( $this->callback_response ) ) {
			$response_type = 'plain';

		} else {
			// For everything else, i.e. null, WP_Error object, caught exception, etc.
			$response_type = 'error';
		}

		return $response_type;
	}

	/**
	 * Sends the appropriate HTTP headers for the response type.
	 */
	protected function send_response_headers() {

		$http_content_type = 'text/plain';

		// Set an HTTP error code if the response type is an error.
		if ( 'error' === $this->response_type ) {
			http_response_code( 500 ); // Internal server error.
		}

		// Set the content type to JSON if the callback response is an array.
		if ( 'json' === $this->response_type ) {
			$http_content_type = 'application/json';
		}

		header( sprintf( 'Content-Type: %s; charset=%s',
			$http_content_type,
			get_option( 'blog_charset' )
		) );
	}

	/**
	 * Sends the response body in the appropriate format.
	 */
	protected function send_response_body() {

		if ( 'json' === $this->response_type ) {

			$response_json = wp_json_encode( $this->callback_response );

			if ( false !== $response_json ) {
				echo $response_json;
			}

		} elseif ( 'plain' === $this->response_type ) {
			echo $this->callback_response;
		}
	}
}
