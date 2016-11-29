<?php
namespace mprm_menu_cart\classes;

/**
 * Class Settings
 */
class Settings {

	protected static $instance;

	protected $option_slug;

	/**
	 * Settings constructor.
	 */
	public function __construct() {
		// Registers settings
		$this->option_slug = 'mp_menu_settings';
	}

	/**
	 * Get instance
	 *
	 * @return Settings
	 */
	public static function get_instance() {
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Init action
	 */
	public function init_action() {
		add_action('admin_menu', array($this, 'add_admin_menu'));
		add_action('admin_init', array($this, 'init_settings'));
		add_action('wp_enqueue_scripts', array($this, 'load_styles'));
		add_action('admin_enqueue_scripts', array($this, 'load_admin_styles'));
		add_action('init', array($this, 'filter_nav_menus'));
	}

	/**
	 * Radio callback
	 *
	 * @param $args
	 */
	public function radio_callback($args) {
		global $mp_menu_options;
		foreach ($args['options'] as $key => $option) :
			$checked = false;
			if (isset($mp_menu_options[$args['id']]) && $mp_menu_options[$args['id']] == $key)
				$checked = true;
			elseif (isset($args['std']) && $args['std'] == $key && !isset($mp_menu_options[$args['id']]))
				$checked = true;
			echo '<input name="' . $this->option_slug . '[' . sanitize_key($args['id']) . ']" id="' . $this->option_slug . '[' . sanitize_key($args['id']) . '][' . sanitize_key($key) . ']" type="radio" value="' . sanitize_key($key) . '" ' . checked(true, $checked, false) . '/>&nbsp;';
			echo '<label for="' . $this->option_slug . '[' . sanitize_key($args['id']) . '][' . sanitize_key($key) . ']">' . esc_html($option) . '</label><br/>';
		endforeach;
		echo '<p class="description">' . wp_kses_post($args['desc']) . '</p>';
	}

	/**
	 * Radio callback
	 *
	 * @param $args
	 */
	public function radio_icons_callback($args) {
		global $mp_menu_options;
		foreach ($args['options'] as $key => $option) :
			$checked = false;
			if (isset($mp_menu_options[$args['id']]) && $mp_menu_options[$args['id']] == $key)
				$checked = true;
			elseif (isset($args['std']) && $args['std'] == $key && !isset($mp_menu_options[$args['id']]))
				$checked = true;
			echo '<input class="mprm-admin-icon" name="' . $this->option_slug . '[' . sanitize_key($args['id']) . ']" id="' . $this->option_slug . '[' . sanitize_key($args['id']) . '][' . sanitize_key($key) . ']" type="radio" value="' . sanitize_key($key) . '" ' . checked(true, $checked, false) . '/>';
			echo '<label class="mprm-admin-icon" for="' . $this->option_slug . '[' . sanitize_key($args['id']) . '][' . sanitize_key($key) . ']">' . $option . '</label>';
		endforeach;
		echo '<p class="description">' . wp_kses_post($args['desc']) . '</p>';
	}

	/**
	 * Text callback
	 *
	 * @param $args
	 */
	public function text_callback($args) {
		global $mp_menu_options;
		if (isset($mp_menu_options[$args['id']])) {
			$value = $mp_menu_options[$args['id']];
		} else {
			$value = isset($args['std']) ? $args['std'] : '';
		}
		if (isset($args['faux']) && true === $args['faux']) {
			$args['readonly'] = true;
			$value = isset($args['std']) ? $args['std'] : '';
			$name = '';
		} else {
			$name = 'name="' . $this->option_slug . '[' . esc_attr($args['id']) . ']"';
		}
		$placeholder = !isset($args['placeholder']) ? '' : $args['placeholder'];

		$readonly = $args['readonly'] === true ? ' readonly="readonly"' : '';
		$size = (isset($args['size']) && !is_null($args['size'])) ? $args['size'] : 'regular';
		$html = '<input type="text" class="' . sanitize_html_class($size) . '-text" id="' . $this->option_slug . '[' . sanitize_key($args['id']) . ']" ' . $name . ' placeholder="' . esc_attr(stripslashes($placeholder)) . '"' . ' value="' . esc_attr(stripslashes($value)) . '"' . $readonly . '/>';
		$html .= '<label for="' . $this->option_slug . '[' . sanitize_key($args['id']) . ']"> ' . wp_kses_post($args['desc']) . '</label>';
		echo $html;
	}

	/**
	 * Select callback
	 *
	 * @param $args
	 */
	public function select_callback($args) {
		global $mp_menu_options;
		if (isset($mp_menu_options[$args['id']])) {
			$value = $mp_menu_options[$args['id']];
		} else {
			$value = isset($args['std']) ? $args['std'] : '';
		}
		if (isset($args['placeholder'])) {
			$placeholder = $args['placeholder'];
		} else {
			$placeholder = '';
		}
		if (isset($args['readonly']) && ($args['readonly'] == true)) {
			$disabled = 'disabled="disabled"';
		} else {
			$disabled = '';
		}

		if (isset($args['chosen']) && $args['chosen']) {
			$chosen = 'class="mprm-chosen mprm-select-chosen"';
		} else {
			$chosen = '';
		}
		$html = '<select id="' . $this->option_slug . '[' . sanitize_key($args['id']) . ']" ' . $disabled . ' name="' . $this->option_slug . '[' . esc_attr($args['id']) . ']" ' . $chosen . 'data-placeholder="' . esc_html($placeholder) . '" />';
		foreach ($args['options'] as $option => $name) {
			$selected = selected($option, $value, false);
			$html .= '<option value="' . esc_attr($option) . '" ' . $selected . '>' . esc_html($name) . '</option>';
		}
		$html .= '</select>';
		$html .= '<label for="' . $this->option_slug . '[' . sanitize_key($args['id']) . ']"> ' . wp_kses_post($args['desc']) . '</label>';
		echo $html;
	}

	/**
	 * Check callback
	 *
	 * @param $args
	 */
	public function checkbox_callback($args) {
		global $mp_menu_options;
		if (isset($args['faux']) && true === $args['faux']) {
			$name = '';
		} else {
			$name = 'name="' . $this->option_slug . '[' . sanitize_key($args['id']) . ']"';
		}
		$checked = isset($mp_menu_options[$args['id']]) ? checked(1, $mp_menu_options[$args['id']], false) : '';
		$html = '<input type="checkbox" id="' . $this->option_slug . '[' . sanitize_key($args['id']) . ']"' . $name . ' value="1" ' . $checked . '/>';
		$html .= '<label for="' . $this->option_slug . '[' . sanitize_key($args['id']) . ']"> ' . wp_kses_post($args['desc']) . '</label>';
		echo $html;
	}

	/**
	 * Missing_callback
	 *
	 * @param $args
	 */
	public function missing_callback($args) {
		printf(
			__('The callback function used for the %s setting is missing.', 'mprm-menu-cart'),
			'<strong>' . $args['id'] . '</strong>'
		);
	}

	/**
	 *  Add admin in menu
	 */
	public function add_admin_menu() {
		add_menu_page(__('menu cart menu', 'mprm-menu-cart'), __('Restaurant menu cart menu', 'mprm-menu-cart'), 'manage_options', 'mprm_menu_cart', array($this, 'options_page'));
	}

	/**
	 * Load scripts styles
	 */
	public function load_styles() {
		wp_enqueue_style('mp-menu-cart-icons', MP_MENU_ASSETS_URL . 'css/style.css', array(), '', 'all');
	}

	/**
	 * Load scripts styles
	 */
	public function load_admin_styles() {
		global $current_screen;
		if (!empty($current_screen) && $current_screen->base == 'toplevel_page_mprm_menu_cart') {
			wp_enqueue_style('mp-menu-cart-icons', MP_MENU_ASSETS_URL . 'css/style.css', array(), '', 'all');
			wp_enqueue_style('mp-menu-admin-styles', MP_MENU_ASSETS_URL . 'css/admin-styles.css', array(), '', 'all');
		}
	}

	/**
	 * Add settings.
	 */
	public function init_settings() {

		if (false == get_option($this->option_slug)) {
			add_option($this->option_slug);
		}

		foreach ($this->get_registered_settings() as $tab => $sections) {
			foreach ($sections as $section => $settings) {
				// Check for backwards compatibility
				$section_tabs = $this->get_settings_tab_sections($tab);
				if (!is_array($section_tabs) || !array_key_exists($section, $section_tabs)) {
					$section = 'main';
					$settings = $sections;
				}
				add_settings_section('mp_menu_settings_' . $tab . '_' . $section, __return_null(), '__return_false', 'mp_menu_settings_' . $tab . '_' . $section);

				foreach ($settings as $option) {
					// For backwards compatibility
					if (empty($option['id'])) {
						continue;
					}
					$name = isset($option['name']) ? $option['name'] : '';
					add_settings_field(
						'mp_menu_settings[' . $option['id'] . ']',
						$name,
						method_exists($this, $option['type'] . '_callback') ? array($this, $option['type'] . '_callback') : array($this, 'missing_callback'),
						'mp_menu_settings_' . $tab . '_' . $section,
						'mp_menu_settings_' . $tab . '_' . $section,
						array(
							'section' => $section,
							'id' => isset($option['id']) ? $option['id'] : null,
							'desc' => !empty($option['desc']) ? $option['desc'] : '',
							'name' => isset($option['name']) ? $option['name'] : null,
							'size' => isset($option['size']) ? $option['size'] : null,
							'options' => isset($option['options']) ? $option['options'] : '',
							'std' => isset($option['std']) ? $option['std'] : '',
							'min' => isset($option['min']) ? $option['min'] : null,
							'max' => isset($option['max']) ? $option['max'] : null,
							'step' => isset($option['step']) ? $option['step'] : null,
							'chosen' => isset($option['chosen']) ? $option['chosen'] : null,
							'placeholder' => isset($option['placeholder']) ? $option['placeholder'] : null,
							'allow_blank' => isset($option['allow_blank']) ? $option['allow_blank'] : true,
							'readonly' => isset($option['readonly']) ? $option['readonly'] : false,
							'faux' => isset($option['faux']) ? $option['faux'] : false,
						)
					);
				}
			}
		}

		register_setting($this->option_slug, 'mp_menu_settings', array($this, 'settings_sanitize'));
	}

	/**
	 * Register settings
	 *
	 * @return mixed
	 */
	public function get_registered_settings() {
		$settings = array(
			/** General Settings */
			'general' => apply_filters('mp_menu_settings_general',
				array(
					'main' => array(
						'mpme_select_menu_id' => array(
							'id' => 'mpme_select_menu_id',
							'name' => __('Select the menu', 'mprm-menu-cart'),
							'desc' => __('Select the menu(s) in which you want to display the Menu Cart', 'mprm-menu-cart'),
							'type' => 'select',
							'options' => $this->get_menu_array(),
							'chosen' => false,
							'placeholder' => __('Select a menu', 'mprm-menu-cart'),
						),
						'mpme_always_display' => array(
							'id' => 'mpme_always_display',
							'name' => __('Always display cart, even if it\'s empty', 'mprm-menu-cart'),
							'type' => 'checkbox',
							'desc' => __('', 'mprm-menu-cart'),
						),
						'mpme_icon_display' => array(
							'id' => 'mpme_icon_display',
							'name' => __('Display shopping cart icon.', 'mprm-menu-cart'),
							'type' => 'checkbox',
							'desc' => __('', 'mprm-menu-cart'),
						),
						'mpme_icon_list' => array(
							'id' => 'mpme_icon_list',
							'name' => __('Choose a cart icon.', 'mprm-menu-cart'),
							'type' => 'radio_icons',
							'options' => $this->get_menu_icons(),
							'desc' => __('', 'mprm-menu-cart'),
						),
						'mpme_display_type' => array(
							'id' => 'mpme_display_type',
							'name' => __('What would you like to display in the menu?', 'mprm-menu-cart'),
							'type' => 'radio',
							'options' => array('items' => 'Items only', 'price' => 'Price only', 'price_and_items' => 'Both price and items'),
							'desc' => __('', 'mprm-menu-cart'),
						),
						'mpme_alignment' => array(
							'id' => 'mpme_alignment',
							'name' => __('Select the alignment that looks best with your menu.', 'mprm-menu-cart'),
							'type' => 'radio',
							'options' => array('left' => 'Align Left.', 'right' => 'Align Right.', 'default' => 'Default Menu Alignment.'),
							'desc' => __('', 'mprm-menu-cart'),
						),
						'mpme_custom_class' => array(
							'id' => 'mpme_custom_class',
							'name' => __('Select the alignment that looks best with your menu.', 'mprm-menu-cart'),
							'type' => 'text',
							'desc' => __('', 'mprm-menu-cart'),
						)
					)
				)
			)
		);
		return apply_filters('mp_menu_registered_settings', $settings);
	}

	/**
	 * Get menu
	 *
	 * @return array
	 */
	public function get_menu_array() {
		$menus = get_terms('nav_menu', array('hide_empty' => false));
		$menu_list = array();

		foreach ($menus as $menu) {
			$menu_list[$menu->slug] = $menu->name;
		}

		return $menu_list;
	}

	/**
	 * Cart icons list
	 *
	 * @return array
	 */
	public function get_menu_icons() {
		$menu_icons_list = array(
			'0' => '<i class="mprm-cart-font icon-mprm-cart0"></i>',
			'1' => '<i class="mprm-cart-font icon-mprm-cart1"></i>',
			'3' => '<i class="mprm-cart-font icon-mprm-cart3"></i>',
			'4' => '<i class="mprm-cart-font icon-mprm-cart4"></i>',
			'5' => '<i class="mprm-cart-font icon-mprm-cart5"></i>',
			'6' => '<i class="mprm-cart-font icon-mprm-cart6"></i>',
			'7' => '<i class="mprm-cart-font icon-mprm-cart7"></i>',
			'8' => '<i class="mprm-cart-font icon-mprm-cart8"></i>'
		);
		return $menu_icons_list;
	}

	/**
	 * Settings tab
	 *
	 * @param $tab
	 *
	 * @return array/bool
	 */
	public function get_settings_tab_sections($tab) {
		$tabs = false;
		$sections = $this->get_registered_settings_sections();

		if ($tab && !empty($sections[$tab])) {
			$tabs = $sections[$tab];
		} else if ($tab) {
			$tabs = false;
		}

		return $tabs;
	}

	/**
	 * Registered sections
	 *
	 * @return array|bool|mixed
	 */
	public function get_registered_settings_sections() {
		$sections = false;

		if (false !== $sections) {
			return $sections;
		}

		$sections = array(
			'general' => apply_filters('mp_menu_settings_sections_general', array(
				'main' => __('General', 'mprm-menu-cart'),
			))
		);

		$sections = apply_filters('mp_menu_settings_sections', $sections);

		return $sections;
	}

	/**
	 * Settings sanitize
	 *
	 * @param array $input
	 *
	 * @return array|mixed
	 */
	public function settings_sanitize($input = array()) {
		global $mp_menu_options;

		if (empty($mp_menu_options)) {
			$mp_menu_options = array();
		}

		if (empty($_POST['_wp_http_referer'])) {
			return $input;
		}

		parse_str($_POST['_wp_http_referer'], $referrer);

		$settings = $this->get_registered_settings();

		$tab = isset($referrer['tab']) ? $referrer['tab'] : 'general';

		$section = isset($referrer['section']) ? $referrer['section'] : 'main';

		$input = $input ? $input : array();
		$input = apply_filters('mp_menu_settings_' . $tab . '-' . $section . '_sanitize', $input);

		if ('main' === $section) {
			// Check for extensions that aren't using new sections
			$input = apply_filters('mp_menu_settings_' . $tab . '_sanitize', $input);
		}

		// Loop through each setting being saved and pass it through a sanitization filter
		foreach ($input as $key => $value) {
			// Get the setting type (checkbox, select, etc)
			$type = isset($settings[$tab][$key]['type']) ? $settings[$tab][$key]['type'] : false;

			if ($type) {
				// Field type specific filter
				$input[$key] = apply_filters('mp_menu_settings_sanitize_' . $type, $value, $key);
			}

			// General filter
			$input[$key] = apply_filters('mp_menu_settings_sanitize', $input[$key], $key);
		}

		// Loop through the whitelist and unset any that are empty for the tab being saved
		$main_settings = $section == 'main' ? $settings[$tab] : array(); // Check for extensions that aren't using new sections
		$section_settings = !empty($settings[$tab][$section]) ? $settings[$tab][$section] : array();
		$found_settings = array_merge($main_settings, $section_settings);

		if (!empty($found_settings)) {

			foreach ($found_settings as $key => $value) {
				// settings used to have numeric keys, now they have keys that match the option ID. This ensures both methods work
				if (is_numeric($key)) {
					$key = $value['id'];
				}
				if (empty($input[$key])) {
					unset($mp_menu_options[$key]);
				}
			}
		}
		// Merge our new settings with the existing
		$output = array_merge($mp_menu_options, $input);

		add_settings_error('mprm-notices', '', __('Settings updated.', 'mprm-menu-cart'), 'updated');

		return $output;
	}

	/**
	 * Options page
	 *
	 * Render settings page
	 */
	public function options_page() {
		$data['settings_tabs'] = $settings_tabs = $this->get_settings_tabs();
		$settings_tabs = empty($settings_tabs) ? array() : $settings_tabs;
		$key = 'main';
		$data['active_tab'] = isset($_GET['tab']) && array_key_exists($_GET['tab'], $settings_tabs) ? $_GET['tab'] : 'general';
		$data['sections'] = $this->get_settings_tab_sections($data['active_tab']);
		$data['section'] = isset($_GET['section']) && !empty($data['sections']) && array_key_exists($_GET['section'], $data['sections']) ? $_GET['section'] : $key;

		echo View::get_instance()->get_template_html('settings', $data);
	}

	/**
	 * Get settings tabs
	 *
	 * @return mixed
	 */
	public function get_settings_tabs() {
		$tabs = array();
		$tabs['general'] = __('General', 'mprm-menu-cart');
		return apply_filters('mp_menu_settings_tabs', $tabs);
	}

	/**
	 * Add filters to selected menus to add cart item <li>
	 */
	public function filter_nav_menus() {
		$mpme_select_menu_id = $this->get_settings('mpme_select_menu_id');
		add_filter('wp_nav_menu_' . $mpme_select_menu_id . '_items', array($this, 'add_item_cart_to_menu'), 10, 2);
	}

	/**
	 * Get settings
	 *
	 * @param bool $key
	 *
	 * @return array|mixed
	 */
	public function get_settings($key = false) {
		global $mp_menu_options;

		$default_settings = $this->default_settings();
		$mp_menu_options = get_option($this->option_slug, array());

		if (empty($mp_menu_options)) {
			$mp_menu_options = array_merge($default_settings, $mp_menu_options);
		}

		if (!empty($mp_menu_options[$key])) {
			return $mp_menu_options[$key];
		} else {
			return $mp_menu_options;
		}
	}

	/**
	 * Default settings.
	 */
	public function default_settings() {
		$default_settings = array(
			'mpme_always_display' => '0',
			'mpme_icon_display' => '1',
			'mpme_icon_list' => '0',
			'mpme_display_type' => 'items',
			'mpme_alignment' => 'default'
		);
		return $default_settings;
	}

	/**
	 * Add cart to menu
	 *
	 * @param $items
	 *
	 * @return mixed
	 */
	public function add_item_cart_to_menu($items) {
		$custom_class = $this->get_option('mpme_custom_class', '');
		$classes = 'mp-menu-cart-li mp-cart-display-' . $this->get_option('mpme_alignment', 'default') . ' ' . $custom_class;

		if ($this->get_common_li_classes($items) != '') {
			$classes .= ' ' . $this->get_common_li_classes($items);
		}

		$classes = apply_filters('mp_menu_item_classes', $classes);
		$mp_menu_item = apply_filters('mp_menu_item_filter', $this->cart_menu_item());
		$menu_item_li = '<li class="' . $classes . '" id="mp-menu-">' . $mp_menu_item . '</li>';

		if (apply_filters('mp_prepend_menu_item', false)) {
			$items = apply_filters('mp_menu_item_wrapper', $menu_item_li) . $items;
		} else {
			$items .= apply_filters('mp_menu_item_wrapper', $menu_item_li);
		}

		return $items;
	}

	/**
	 * @param string $key
	 * @param bool $default
	 *
	 * @return mixed
	 */
	public function get_option($key = '', $default = false) {
		global $mp_menu_options;

		if (empty($mp_menu_options)) {
			$mp_menu_options = $this->get_settings();
		}

		$value = !empty($mp_menu_options[$key]) ? $mp_menu_options[$key] : $default;
		$value = apply_filters('mp_menu_get_option', $value, $key, $default);

		return apply_filters('mp_menu_get_option_' . $key, $value, $key, $default);
	}

	/**
	 * Common li classes
	 *
	 * @param $items
	 *
	 * @return string
	 */
	public function get_common_li_classes($items) {
		if (empty($items)) {
			return '';
		}

		$libxml_previous_state = libxml_use_internal_errors(true); // enable user error handling

		$dom_items = new \DOMDocument;
		$dom_items->loadHTML($items);
		$lis = $dom_items->getElementsByTagName('li');

		if (empty($lis)) {
			libxml_clear_errors();
			libxml_use_internal_errors($libxml_previous_state);
			return '';
		}

		foreach ($lis as $li) {
			if ($li->parentNode->tagName != 'ul')
				$li_classes[] = explode(' ', $li->getAttribute('class'));
		}
		// clear errors and reset to previous error handling state
		libxml_clear_errors();
		libxml_use_internal_errors($libxml_previous_state);

		if (!empty($li_classes)) {
			$common_li_classes = array_shift($li_classes);
			foreach ($li_classes as $li_class) {
				$common_li_classes = array_intersect($li_class, $common_li_classes);
			}
			$common_li_classes_flat = implode(' ', $common_li_classes);
		} else {
			$common_li_classes_flat = '';
		}

		return $common_li_classes_flat;
	}

	/**
	 * Create HTML for Menu Cart item
	 */
	public function cart_menu_item() {

		$item_data = $this->get_menu_item();

		$always_display = $this->get_option('mpme_always_display', false);
		$icon_display = $this->get_option('mpme_icon_display', false);
		$display_type = $this->get_option('mpme_display_type', 'items');

		// Check empty cart settings
		if ($item_data['cart_contents_count'] == 0 && (!$always_display)) {
			$empty_menu_item = '<a class="mp-menu-cart-contents empty-mp-menu-cart" style="display:none">&nbsp;</a>';
			return $empty_menu_item;
		}

		if (isset($this->options['wpml_string_translation']) && function_exists('icl_t')) {
			//use WPML
			$viewing_cart = icl_t('WP Menu Cart', 'hover text', 'View your shopping cart');
			$start_shopping = icl_t('WP Menu Cart', 'empty hover text', 'Start shopping');
			$cart_contents = $item_data['cart_contents_count'] . ' ' . ($item_data['cart_contents_count'] == 1 ? icl_t('WP Menu Cart', 'item text', 'item') : icl_t('WP Menu Cart', 'items text', 'items'));
		} else {
			//use regular WP i18n
			$viewing_cart = __('View your shopping cart', 'mprm-menu-cart');
			$start_shopping = __('Start shopping', 'mprm-menu-cart');
			$cart_contents = sprintf(_n('%d item', '%d items', $item_data['cart_contents_count'], 'mprm-menu-cart'), $item_data['cart_contents_count']);
		}

		$this->menu_items['menu']['cart_contents'] = $cart_contents;

		if ($item_data['cart_contents_count'] == 0) {
			$menu_item_href = apply_filters('mp-menu-cart_empty-url', $item_data['shop_page_url']);
			$menu_item_title = apply_filters('mp-menu-cart_empty-title', $start_shopping);
			$menu_item_classes = 'mp-menu-cart-contents empty-mp-menu-cart-visible';
		} else {
			$menu_item_href = apply_filters('mp-menu-cart_full_url', $item_data['cart_url']);
			$menu_item_title = apply_filters('mp-menu-cart_full_title', $viewing_cart);
			$menu_item_classes = 'mp-menu-cart-contents';
		}

		$this->menu_items['menu']['menu_item_href'] = $menu_item_href;
		$this->menu_items['menu']['menu_item_title'] = $menu_item_title;
		$menu_item = '<a class="' . $menu_item_classes . '" href="' . $menu_item_href . '" title="' . $menu_item_title . '">';

		$menu_item_a_content = '';

		if ($icon_display) {
			$icon_list = $this->get_settings('mpme_icon_list');
			$icon = isset($icon_list) ? $icon_list : '0';
			$menu_item_icon = '<i class="mprm-cart-font icon-mprm-cart' . $icon . '"></i>';
			$menu_item_a_content .= $menu_item_icon;
		} else {
			$menu_item_icon = '';
		}

		switch ($display_type) {
			case 'items': //items only
				$menu_item_a_content .= '<span class="mp-menu-cart-contents">' . $cart_contents . '</span>';
				break;
			case 'price': //price only
				$menu_item_a_content .= '<span class="mp-menu-cart-amount">' . $item_data['cart_total'] . '</span>';
				break;
			case 'price_and_items': //items & price
				$menu_item_a_content .= '<span class="mp-menu-cart-contents">' . $cart_contents . '</span><span class="mp-menu-cart-amount">' . $item_data['cart_total'] . '</span>';
				break;
		}

		$menu_item_a_content = apply_filters('mp_menu_item_a_content', $menu_item_a_content, $menu_item_icon, $cart_contents, $item_data);

		$this->menu_items['menu']['menu_item_a_content'] = $menu_item_a_content;

		$menu_item .= $menu_item_a_content . '</a>';

		$menu_item = apply_filters('mp_menu_item_a', $menu_item, $item_data, $this->get_settings(), $menu_item_a_content, $viewing_cart, $start_shopping, $cart_contents);

		if (!empty($menu_item)) {
			return $menu_item;
		}
	}

	/**
	 * Menu item
	 *
	 * @return array
	 */
	public function get_menu_item() {
		$menu_item = array(
			'cart_url' => mprm_get_checkout_uri(),
			'shop_page_url' => get_home_url(),
			'cart_contents_count' => mprm_get_cart_quantity(),
			'cart_total' => mprm_currency_filter(mprm_format_amount(mprm_get_cart_total())),
		);
		return $menu_item;
	}
}