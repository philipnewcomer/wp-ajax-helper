<?php
/**
 * WP_Ajax_Helper validations
 *
 * @package PhilipNewcomer\WP_Ajax_Helper
 */

namespace PhilipNewcomer\WP_Ajax_Helper;

/**
 * Class Validations
 */
class Validations {

	/**
	 * Tests the user login state.
	 *
	 * @param bool $login_state The login state to test.
	 *
	 * @return bool
	 */
	public function logged_in( $login_state ) {

		return is_user_logged_in() === $login_state;
	}

	/**
	 * Tests whether the current user has the specified capability.
	 *
	 * @param string $capability The capability to test.
	 *
	 * @return bool
	 */
	public function user_can( $capability ) {

		if ( ! is_user_logged_in() ) {
			return false;
		}

		return current_user_can( $capability );
	}

	/**
	 * Tests whether the current user has the specified role.
	 *
	 * @param string $role The role to test.
	 *
	 * @return bool
	 */
	public function user_is( $role ) {

		if ( ! is_user_logged_in() ) {
			return false;
		}

		$user = wp_get_current_user();
		return in_array( $role, $user->roles );
	}
}
