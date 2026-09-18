<?php
namespace mprm_menu_cart\classes;
/**
 * View class
 */
class View extends \mp_restaurant_menu\classes\View {

	protected static $instance;

	public function __construct() {
		$this->template_path = MP_MENU_TEMPLATE_PATH;
		$this->templates_path = MP_MENU_TEMPLATES_PATH;
		$this->prefix = 'mp_menu';
	}

	/**
	 * Determine whether a template belongs to this addon.
	 *
	 * The Restaurant Menu View validates templates against the main plugin
	 * path. This subclass uses the Menu Cart plugin path instead.
	 *
	 * @param string $template_name Template name including its extension.
	 * @return bool
	 */
	protected function is_plugin_template($template_name) {
		$plugin_path = realpath(MP_MENU_PLUGIN_PATH);
		$template_path = realpath($this->templates_path . ltrim($template_name, '/'));

		return (
			false !== $plugin_path &&
			false !== $template_path &&
			0 === strpos($template_path, trailingslashit($plugin_path)) &&
			is_file($template_path)
		);
	}

	/**
	 * @return View
	 */
	public static function get_instance() {
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}
