<?php
/**
 * WP_Ajax_Helper handler
 *
 * @package PhilipNewcomer\WP_Ajax_Helper
 */

namespace PhilipNewcomer\WP_Ajax_Helper;

/**
 * Class Handler
 */
class Handler {

	/**
	 * The handle for this AJAX Helper instance.
	 *
	 * @var string
	 */
	protected $handle;

	/**
	 * The handle's callback function.
	 *
	 * @var mixed
	 */
	protected $handle_callback;

	/**
	 * The arguments to be passed to the handle's callback function.
	 *
	 * @var array
	 */
	protected $handle_callback_args;

	/**
	 * The frontend class.
	 *
	 * @var Frontend
	 */
	protected $frontend;

	/**
	 * The responder class.
	 *
	 * @var Responder
	 */
	protected $responder;

	/**
	 * The utility class.
	 *
	 * @var Utility
	 */
	protected $utility;

	/**
	 * The validator class.
	 *
	 * @var Validator
	 */
	protected $validator;

	/**
	 * Handler constructor.
	 *
	 * @param Responder $responder The responder class.
	 * @param Validator $validator The validator class.
	 * @param Frontend  $frontend  The frontend class.
	 * @param Utility   $utility   The utility class.
	 */
	public function __construct( Responder $responder, Validator $validator, Frontend $frontend, Utility $utility ) {

		$this->frontend    = $frontend;
		$this->responder   = $responder;
		$this->utility     = $utility;
		$this->validator   = $validator;
	}

	/**
	 * The main handler for the specfied handle.
	 *
	 * @param string $handle The instance handle.
	 *
	 * @return Handler
	 */
	public function handle( $handle ) {

		// Sanitize the handle name.
		$this->handle = $this->utility->sanitize_handle( $handle );

		// Register this handle with the frontend class.
		$this->frontend->register_handle( $this->handle );

		// Register the Ajax handler in the WordPress hook system.
		add_action( 'wp_ajax_'        . $this->handle, array( $this, 'ajax_handler' ) );
		add_action( 'wp_ajax_nopriv_' . $this->handle, array( $this, 'ajax_handler' ) );

		return $this;
	}

	/**
	 * Handles the Ajax action.
	 */
	public function ajax_handler() {

		if ( $this->utility->validate_nonce( $this->handle ) && $this->validator->validate_all() ) {

			$callback_response = $this->utility->get_callback_response( $this->handle, $this->handle_callback, $this->handle_callback_args );
			$this->responder->handle_response( $callback_response );

		} else {

			http_response_code( 403 ); // Forbidden.
		}

		die();
	}

	/**
	 * Registers the callback function to handle the request.
	 *
	 * @param mixed $callback      The callback function (function name, class method, or closure).
	 * @param array $callback_args Arguments to be passed to the callback.
	 *
	 * @return Handler
	 */
	public function with_callback( $callback = '', $callback_args = array() ) {

		$this->handle_callback      = $callback;
		$this->handle_callback_args = $callback_args;

		return $this;
	}

	/**
	 * Registers the validation settings.
	 *
	 * @param array $validations The validation settings.
	 *
	 * @return Handler
	 */
	public function with_validation( $validations = array() ) {

		foreach ( (array) $validations as $condition => $value ) {
			$this->validator->add_validation( $condition, $value );
		}

		return $this;
	}
}
