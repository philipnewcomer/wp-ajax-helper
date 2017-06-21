<?php
/**
 * WP_Ajax_Helper frontend
 *
 * @package PhilipNewcomer\WP_Ajax_Helper;
 */

namespace PhilipNewcomer\WP_Ajax_Helper;

/**
 * Class Frontend
 */
class Frontend {

	/**
	 * The singleton instance.
	 *
	 * @var Frontend
	 */
	private static $instance;

	/**
	 * The registered handles.
	 *
	 * @var array
	 */
	protected $registered_handles = array();

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @return Frontend
	 */
	public static function get_instance() {

		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Registers an instance handle.
	 *
	 * @param string $handle The handle name.
	 */
	public function register_handle( $handle = '' ) {

		$this->registered_handles[] = $handle;

		// Only call add_actions() the first time a handle is registered, as the actions will still be registered for
		// subsequent handles.
		if ( 1 === count( $this->registered_handles ) ) {
			$this->add_actions();
		}
	}

	/**
	 * Registers the class's actions in the WordPress hook system.
	 */
	protected function add_actions() {
		add_action( 'wp_enqueue_scripts',    array( static::$instance, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( static::$instance, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueues the library's scripts.
	 */
	public function enqueue_scripts() {

		if ( empty( $this->registered_handles ) ) {
			return;
		}

//		$script_url = home_url( str_replace( ABSPATH, '', dirname( dirname( __DIR__ ) ) ) . '/assets/js/wp-ajax-helper.js' );

        $abs = self::normalize_path( ABSPATH);
        $script_url = home_url( str_replace( $abs, '', self::normalize_path(dirname( dirname( __DIR__ ) )) ) . '/assets/js/wp-ajax-helper.js' );

		wp_enqueue_script( 'wp-ajax-helper', $script_url, array( 'jquery' ), null, true );

		wp_localize_script(
			'wp-ajax-helper',
			'wpAjaxHelper',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'handles' => $this->get_nonces(),
			)
		);
	}

	/**
	 * Generates nonces for each of the registered handles.
	 *
	 * @return array The handles with their associated nonces.
	 */
	protected function get_nonces() {
		$nonces = array();

		foreach ( (array) $this->registered_handles as $handle ) {
			$nonces[ $handle ] = wp_create_nonce( $handle );
		}

		return $nonces;
	}

    /**
     * `wp_normalize_path` wrapper for back-compat. Normalize a filesystem path.
     *
     * On windows systems, replaces backslashes with forward slashes
     * and forces upper-case drive letters.
     * Allows for two leading slashes for Windows network shares, but
     * ensures that all other duplicate slashes are reduced to a single.
     * @param string $path Path to normalize.
     * @return string Normalized path.
     */
    protected static function normalize_path( $path ) {
        if ( function_exists( 'wp_normalize_path' ) ) {
            return wp_normalize_path( $path );
        }
        // Replace newer WP's version of wp_normalize_path.
        $path = str_replace( '\\', '/', $path );
        $path = preg_replace( '|(?<=.)/+|', '/', $path );
        if ( ':' === substr( $path, 1, 1 ) ) {
            $path = ucfirst( $path );
        }
        return $path;
    }
}
