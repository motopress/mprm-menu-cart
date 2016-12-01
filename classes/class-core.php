<?php
namespace mprm_menu_cart\classes;

use mprm_menu_cart\classes\models\Menu_cart;
use mprm_menu_cart\classes\models\Settings;

/**
 * Class Core
 *
 * @package mprm_menu_cart\classes
 */
class Core extends \mp_restaurant_menu\classes\Core {

	protected static $instance;
	/**
	 * Current state
	 */
	private $state;

	private $version;

	/**
	 * Core constructor.
	 */
	public function __construct() {
		$this->state = new State_Factory(dirname(__NAMESPACE__));
		$this->init_plugin_version();
	}

	/**
	 *  Get plugin version
	 */
	public function init_plugin_version() {
		$filePath = MP_MENU_PLUGIN_PATH . 'mprm-menu-cart.php';
		if (!$this->version) {
			$pluginObject = get_plugin_data($filePath);
			$this->version = $pluginObject['Version'];
		}
	}

	/**
	 * Get instance Core Object
	 *
	 * @return Core
	 */
	public static function get_instance() {
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Init plugin files and hooks
	 *
	 * @param $name
	 */
	public function init_plugin($name) {
		Core::include_all(MP_MENU_PLUGIN_PATH . 'functions/');

		$this->hooks();
	}

	/**
	 * Hooks
	 */
	public function hooks() {
		add_action('init', array($this, 'wp_ajax_route_url'), 0);
		add_action('admin_init', array(Settings::get_instance(), 'init_settings'));
		add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
		add_action('wp_enqueue_scripts', array($this, 'add_theme_script'));
		Menu_cart::get_instance()->init_action();
	}

	/**
	 * Ajax route URL
	 */
	public function wp_ajax_route_url() {
		$controller = isset($_REQUEST['mp_menu_controller']) ? $_REQUEST['mp_menu_controller'] : null;
		$action = isset($_REQUEST['mprm_action']) ? $_REQUEST['mprm_action'] : null;
		if (!empty($action) && !empty($controller)) {
			// call controller
			$controller = $this->get_controller($controller);
			if (is_object($controller)) {
				$action = 'action_' . $action;
				$controller->$action();
			}
			die();
		}
	}

	/**
	 * Get controller object
	 *
	 * @param null $type
	 *
	 * @return model|bool
	 */
	public function get_controller($type = null) {
		return $this->get_state()->get_controller($type);
	}

	/**
	 * Get state
	 *
	 * @return bool|State_Factory
	 */
	public function get_state() {
		if ($this->state) {
			return $this->state;
		} else {
			return false;
		}
	}

	/**
	 * Get view
	 *
	 * @return object
	 */
	public function get_view() {
		return View::get_instance();
	}

	/**
	 * Get model instance
	 *
	 * @param bool|false $type
	 *
	 * @return bool|mixed
	 */
	public function get($type = false) {
		$state = false;
		if ($type) {
			$state = $this->get_model($type);
		}
		return $state;
	}

	/**
	 * Check and return current state
	 *
	 * @param string $type
	 *
	 * @return boolean|Model
	 */
	public function get_model($type = null) {
		return $this->get_state()->get_model($type);
	}

	/**
	 * Add theme script
	 */
	public function add_theme_script() {
		wp_enqueue_style('mp-menu-cart-icons', MP_MENU_ASSETS_URL . 'css/style.css', array(), '', 'all');
	}

	/**
	 * Get prefix for JS,CSS
	 *
	 * @return string
	 */
	public function get_prefix() {
		$prefix = !MP_MENU_DEBUG ? '.min' : '';
		return $prefix;
	}

	public function get_version() {
		return $this->version;
	}

	/**
	 * Add js by current screen
	 */
	public function admin_enqueue_scripts() {
		global $current_screen;
		$this->current_screen($current_screen);
	}

	/**
	 * Current screen
	 *
	 * @param \WP_Screen $current_screen
	 */
	public function current_screen(\WP_Screen $current_screen) {
		$tab = !empty($_GET['tab']) ? $_GET['tab'] : false;
		if (!empty($current_screen) && $current_screen->base == 'restaurant-menu_page_mprm-settings') {
			if ($tab == 'menu_cart') {
				wp_enqueue_style('mp-menu-cart-icons', MP_MENU_ASSETS_URL . 'css/style.css', array(), '', 'all');
				wp_enqueue_style('mp-menu-admin-styles', MP_MENU_ASSETS_URL . 'css/admin-styles.css', array(), '', 'all');
			}
		} elseif ($current_screen->base == 'toplevel_page_mprm_menu_cart') {
			wp_enqueue_style('mp-menu-cart-icons', MP_MENU_ASSETS_URL . 'css/style.css', array(), '', 'all');
			wp_enqueue_style('mp-menu-admin-styles', MP_MENU_ASSETS_URL . 'css/admin-styles.css', array(), '', 'all');
		}
	}
}