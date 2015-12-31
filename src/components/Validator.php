<?php
/**
 * WP_Ajax_Helper validator
 *
 * @package PhilipNewcomer\WP_Ajax_Helper
 */

namespace PhilipNewcomer\WP_Ajax_Helper;

/**
 * Class Validator
 */
class Validator {

	/**
	 * The registered validations.
	 *
	 * @var array
	 */
	protected $registered_validations;

	/**
	 * The validations class.
	 *
	 * @var Validations
	 */
	protected $validations_class;

	/**
	 * Validator constructor.
	 *
	 * @param Validations $validations The validations class.
	 */
	public function __construct( Validations $validations ) {

		$this->validations_class = $validations;
	}

	/**
	 * Registers a validation.
	 *
	 * @param string $condition The name of the condition.
	 * @param mixed  $value     The value to validate.
	 */
	public function add_validation( $condition = '', $value = '' ) {

		$this->registered_validations[ $condition ] = $value;
	}

	/**
	 * Determines whether all validations have passed.
	 *
	 * @return bool
	 */
	public function validate_all() {

		foreach ( (array) $this->registered_validations as $condition => $value ) {

			if ( ! $this->validate_single( $condition, $value ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Runs a single validation.
	 *
	 * @param string $condition The name of the condition.
	 * @param mixed  $value     The value to validate.
	 *
	 * @return bool Whether the validation passes.
	 */
	private function validate_single( $condition, $value ) {

		$validator = array( $this->validations_class, $condition );

		if ( is_callable( $validator ) ) {
			$result = call_user_func( $validator, $value );
		} else {
			$result = false;
		}

		return (bool) $result;
	}
}
