<?php

/**
 * The core plugin class
 */
class LBCG {
	/**
	 * An instance of this class
	 *
	 * @var null|object $instance An object of this class
	 */
	private static $instance = null;

	/**
	 * Plugin all needed properties in one place
	 *
	 * @var array $attributes The array containing main attributes of the plugin.
	 */
	protected $attributes = [];

	/**
	 * Get instance
	 *
	 * @param   string  $version
	 * @param   string  $plugin_dir
	 * @param   string  $plugin_url
	 *
	 * @return LBCG
	 */
	public static function get_instance( $version, $plugin_dir, $plugin_url ) {
		if ( null === self::$instance ) {
			// Create new instance
			self::$instance = new LBCG( $version, $plugin_dir, $plugin_url );
		}

		return self::$instance;
	}

	/**
	 * Construct Bingo Card object
	 *
	 * @param   string  $version
	 * @param   string  $plugin_dir
	 * @param   string  $plugin_url
	 */
	private function __construct( $version, $plugin_dir, $plugin_url ) {
		// Set plugin path and url params
		$this->attributes = [
			'plugin_version'        => $version,
			'plugin_path'           => $plugin_dir,
			'plugin_url'            => $plugin_url,
			'logs_path'             => $plugin_dir . 'logs',
			'admin_path'            => $plugin_dir . 'admin',
			'admin_url'             => $plugin_url . 'admin/',
			'admin_templates_path'  => $plugin_dir . 'admin/partials',
			'ajax_path'             => $plugin_dir . 'ajax',
			'includes_path'         => $plugin_dir . 'includes',
			'includes_url'          => $plugin_url . 'includes/',
			'public_path'           => $plugin_dir . 'public',
			'public_url'            => $plugin_url . 'public/',
			'public_templates_path' => $plugin_dir . 'public/partials'
		];
		// Start
		$this->load_dependencies();
		$this->init();
	}

	/**
	 * Load classes
	 */
	private function load_dependencies() {
		require_once $this->attributes['admin_path'] . '/class-lbcg-admin.php';
		require_once $this->attributes['ajax_path'] . '/class-lbcg-ajax.php';
		require_once $this->attributes['includes_path'] . '/class-lbcg-helper.php';
		require_once $this->attributes['public_path'] . '/class-lbcg-public.php';
	}

	/**
	 * Register dependencies
	 */
	private function init() {
		if ( LBCG_Helper::is_request( 'admin' ) ) {
			$admin_obj = LBCG_Admin::get_instance( $this->attributes );
			$admin_obj->register_dependencies();
		} elseif ( LBCG_Helper::is_request( 'ajax' ) ) {
			$ajax_obj = new LBCG_Ajax( $this->attributes );
			$ajax_obj->register_dependencies();
		} elseif ( LBCG_Helper::is_request( 'public' ) ) {
			$public_obj = LBCG_Public::get_instance( $this->attributes );
			$public_obj->register_dependencies();
		}
	}
}
