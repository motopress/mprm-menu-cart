<?php
namespace mprm_menu_cart\classes\models;

use mp_restaurant_menu\classes\models\Settings as Main_Settings;
use mprm_menu_cart\classes\Model;
use mprm_menu_cart\classes\View;

/**
 * Class Settings
 */
class Settings extends Model {
	
	protected static $instance;
	
	protected $option_slug;
	protected $settings_type = 'extension';
	
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
	 * Radio callback
	 *
	 * @param $args
	 */
	public function radio_callback($args) {

		global $mp_menu_options;

		foreach ($args[ 'options' ] as $key => $option) :
			$checked = false;
			if (isset($mp_menu_options[ $args[ 'id' ] ]) && $mp_menu_options[ $args[ 'id' ] ] == $key)
				$checked = true;
			elseif (isset($args[ 'std' ]) && $args[ 'std' ] == $key && !isset($mp_menu_options[ $args[ 'id' ] ]))
				$checked = true;
			echo '<input class="mprm-admin-icon" name="' . esc_attr( $this->option_slug ) . '[' . esc_attr( $args[ 'id' ] ) . ']" id="' . esc_attr( $this->option_slug ) . '[' . esc_attr( $args[ 'id' ] ) . '][' . esc_attr( $key ) . ']" type="radio" value="' . esc_attr( $key ) . '" ' . checked(true, $checked, false) . '/>';
			echo '<label class="mprm-admin-icon" for="' . esc_attr( $this->option_slug ) . '[' . esc_attr( $args[ 'id' ] ) . '][' . esc_attr( $key ) . ']">' . wp_kses_post( $option ) . '</label><br/>';
		endforeach;

		echo '<p class="description">' . wp_kses_post($args[ 'desc' ]) . '</p>';
	}
	
	/**
	 * Text callback
	 *
	 * @param $args
	 */
	public function text_callback($args) {
		global $mp_menu_options;
		if (isset($mp_menu_options[ $args[ 'id' ] ])) {
			$value = $mp_menu_options[ $args[ 'id' ] ];
		} else {
			$value = isset($args[ 'std' ]) ? $args[ 'std' ] : '';
		}

		$placeholder = !isset($args[ 'placeholder' ]) ? '' : $args[ 'placeholder' ];
		$size = (isset($args[ 'size' ]) && !is_null($args[ 'size' ])) ? $args[ 'size' ] : 'regular';

		?>
		<input type="text" class="<?php echo esc_attr( $size ); ?>-text" id="<?php echo esc_attr( $this->option_slug ); ?>[<?php echo esc_attr( $args[ 'id' ] ); ?>]" 
		<?php
		if (isset($args[ 'faux' ]) && true === $args[ 'faux' ]) {
			$args[ 'readonly' ] = true;
			$value = isset($args[ 'std' ]) ? $args[ 'std' ] : '';
			echo '';
		} else { ?>
			name="<?php echo esc_attr( $this->option_slug ); ?>[<?php echo esc_attr( $args[ 'id' ] ); ?>]"
		<?php } ?>
		placeholder="<?php echo esc_attr( stripslashes( $placeholder ) ); ?>" value="<?php echo esc_attr( stripslashes( $value ) ); ?>" <?php
			if ( $args[ 'readonly' ] === true ) { echo 'readonly="readonly"'; } ?> />
		<label for="<?php echo esc_attr( $this->option_slug ); ?>[<?php echo esc_attr( $args[ 'id' ] ); ?>]"><?php echo wp_kses_post($args[ 'desc' ]); ?></label>
		<?php
	}

	/**
	 * Select callback
	 *
	 * @param $args
	 */
	public function select_callback($args) {
		global $mp_menu_options;
		if (isset($mp_menu_options[ $args[ 'id' ] ])) {
			$value = $mp_menu_options[ $args[ 'id' ] ];
		} else {
			$value = isset($args[ 'std' ]) ? $args[ 'std' ] : '';
		}
		if (isset($args[ 'placeholder' ])) {
			$placeholder = $args[ 'placeholder' ];
		} else {
			$placeholder = '';
		}

		?>
		<select id="<?php echo esc_attr( $this->option_slug ); ?>[<?php echo esc_attr( $args[ 'id' ] ); ?>]"<?php
		if ( isset($args[ 'multiple' ]) && $args[ 'multiple' ] ) {
			echo ' multiple="multiple"';
		}
		if (isset($args[ 'readonly' ]) && ($args[ 'readonly' ] == true)) {
			echo ' disabled="disabled"';
		}
		?> name="<?php echo esc_attr( $this->option_slug ); ?>[<?php echo esc_attr( $args[ 'id' ] ); ?>]" <?php
		if (isset($args[ 'chosen' ]) && $args[ 'chosen' ]) {
			echo ' class="mprm-chosen mprm-select-chosen"';
		}
		?> data-placeholder="<?php echo esc_attr( $placeholder ); ?>" />
		<?php
		foreach ($args[ 'options' ] as $option => $name) {
			?>
			<option value="<?php echo esc_attr( $option ); ?>" <?php selected($option, $value);  ?>><?php echo esc_html( $name ); ?></option>
		<?php } ?>
		</select>
		<label for="<?php echo esc_attr( $this->option_slug ); ?>[<?php echo esc_attr( $args[ 'id' ] ); ?>]"><?php echo wp_kses_post( $args[ 'desc' ] ); ?></label>
		<?php
	}
	
	/**
	 * Check callback
	 *
	 * @param $args
	 */
	public function checkbox_callback($args) {

		global $mp_menu_options;

		?>
		<input type="checkbox" id="<?php echo esc_attr( $this->option_slug ); ?>[<?php echo esc_attr( $args[ 'id' ] ); ?>]" <?php
		if (isset($args[ 'faux' ]) && true === $args[ 'faux' ]) {
			echo '';
		} else {
			echo ' name="' . esc_attr( $this->option_slug ) . '[' . esc_attr( $args[ 'id' ] ) . ']"';
		}
		?> value="1" <?php
			if ( isset($mp_menu_options[ $args[ 'id' ] ] ) ) { checked( 1, $mp_menu_options[ $args[ 'id' ] ] ); }
		?>/>
		<label for="<?php echo esc_attr( $this->option_slug ); ?>[<?php echo esc_attr( $args[ 'id' ] ); ?>]"><?php echo wp_kses_post( $args[ 'desc' ] ); ?></label>
		<?php
	}
	
	/**
	 * Missing_callback
	 *
	 * @param $args
	 */
	public function missing_callback($args) {
		printf(
			esc_html__('The callback function used for the %s setting is missing.', 'mprm-menu-cart'),
			'<strong>' . esc_html( $args[ 'id' ] ) . '</strong>'
		);
	}
	
	/**
	 * Add settings.
	 */
	public function init_settings() {
		if ($this->settings_type != 'extension') {
			$this->get_settings();
			
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
						if (empty($option[ 'id' ])) {
							continue;
						}
						$name = isset($option[ 'name' ]) ? $option[ 'name' ] : '';
						add_settings_field(
							'mp_menu_settings[' . $option[ 'id' ] . ']',
							$name,
							method_exists($this, $option[ 'type' ] . '_callback') ? array($this, $option[ 'type' ] . '_callback') : array($this, 'missing_callback'),
							'mp_menu_settings_' . $tab . '_' . $section,
							'mp_menu_settings_' . $tab . '_' . $section,
							array(
								'section' => $section,
								'id' => isset($option[ 'id' ]) ? $option[ 'id' ] : null,
								'desc' => !empty($option[ 'desc' ]) ? $option[ 'desc' ] : '',
								'name' => isset($option[ 'name' ]) ? $option[ 'name' ] : null,
								'size' => isset($option[ 'size' ]) ? $option[ 'size' ] : null,
								'options' => isset($option[ 'options' ]) ? $option[ 'options' ] : '',
								'std' => isset($option[ 'std' ]) ? $option[ 'std' ] : '',
								'min' => isset($option[ 'min' ]) ? $option[ 'min' ] : null,
								'max' => isset($option[ 'max' ]) ? $option[ 'max' ] : null,
								'step' => isset($option[ 'step' ]) ? $option[ 'step' ] : null,
								'chosen' => isset($option[ 'chosen' ]) ? $option[ 'chosen' ] : null,
								'placeholder' => isset($option[ 'placeholder' ]) ? $option[ 'placeholder' ] : null,
								'allow_blank' => isset($option[ 'allow_blank' ]) ? $option[ 'allow_blank' ] : true,
								'readonly' => isset($option[ 'readonly' ]) ? $option[ 'readonly' ] : false,
								'faux' => isset($option[ 'faux' ]) ? $option[ 'faux' ] : false,
								'multiple' => isset($option[ 'multiple' ]) ? $option[ 'multiple' ] : false,
							)
						);
					}
				}
			}
			register_setting($this->option_slug, 'mp_menu_settings', array($this, 'settings_sanitize'));
		}
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
		if ($this->settings_type == 'extension') {
			$mp_menu_options = array(
				'mpme_always_display' => mprm_get_option('mpme_always_display', '0'),
				'mpme_icon_display' => mprm_get_option('mpme_icon_display', '0'),
				'mpme_icon_list' => mprm_get_option('mpme_icon_list', 'zero'),
				'mpme_display_type' => mprm_get_option('mpme_icon_list', 'items'),
				'mpme_alignment' => mprm_get_option('mpme_icon_list', 'default')
			);
		} else {
			$mp_menu_options = get_option($this->option_slug, array());
			if (empty($mp_menu_options)) {
				$default_settings = $this->default_settings();
				$mp_menu_options = array_merge($default_settings, $mp_menu_options);
			}
		}
		
		if (!empty($mp_menu_options[ $key ])) {
			return $mp_menu_options[ $key ];
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
	 * Register settings
	 *
	 * @return mixed
	 */
	public function get_registered_settings() {
		$settings_list = $this->get_settings_list();
		$settings = array(
			/** General Settings */
			'general' => apply_filters('mp_menu_settings_general',
				array(
					'main' => $settings_list
				)
			)
		);
		
		return apply_filters('mp_menu_registered_settings', $settings);
	}
	
	/**
	 *  Available menu cart settings
	 *
	 * @return array
	 */
	public function get_settings_list() {
		$settings_list = array(
			'mpme_select_menu_id' => array(
				'id' => 'mpme_select_menu_id',
				'name' => esc_html__('Menu', 'mprm-menu-cart'),
				'desc' => '<br>' . esc_html__('Select the menu in which you want to display the cart', 'mprm-menu-cart'),
				'type' => 'select',
				'options' => Menu_cart::get_instance()->get_menu_array(),
				'placeholder' => esc_html__('Select a menu', 'mprm-menu-cart'),
				'chosen' => false
			),
			'mpme_always_display' => array(
				'id' => 'mpme_always_display',
				'name' => esc_html__('Empty cart', 'mprm-menu-cart'),
				'desc' => esc_html__('Always display cart, even if it\'s empty', 'mprm-menu-cart'),
				'type' => 'checkbox'
			),
			'mpme_icon_display' => array(
				'id' => 'mpme_icon_display',
				'name' => esc_html__('Cart icon', 'mprm-menu-cart'),
				'desc' => esc_html__('Display shopping cart icon', 'mprm-menu-cart'),
				'type' => 'checkbox'
			),
			'mpme_icon_list' => array(
				'id' => 'mpme_icon_list',
				'name' => '',
				'type' => 'radio',
				'options' => $this->get_menu_icons(),
				'std' => 'zero',
			),
			'mpme_display_type' => array(
				'id' => 'mpme_display_type',
				'name' => esc_html__('What would you like to display in the menu?', 'mprm-menu-cart'),
				'type' => 'radio',
				'options' => array(
					'items' => esc_html__('Items only', 'mprm-menu-cart'),
					'price' => esc_html__('Price only', 'mprm-menu-cart'),
					'price_and_items' => esc_html__('Both price and items', 'mprm-menu-cart')
				),
				'std' => 'items'
			),
			'mpme_alignment' => array(
				'id' => 'mpme_alignment',
				'name' => esc_html__('Select the alignment that looks best with your menu.', 'mprm-menu-cart'),
				'type' => 'radio',
				'options' => array(
					'left' => esc_html__('Align Left', 'mprm-menu-cart'),
					'right' => esc_html__('Align Right', 'mprm-menu-cart'),
					'default' => esc_html__('Default Menu Alignment', 'mprm-menu-cart')
				),
				'std' => 'default'
			),
			'mpme_custom_class' => array(
				'id' => 'mpme_custom_class',
				'name' => esc_html__('Enter a custom CSS class (optional)', 'mprm-menu-cart'),
				'type' => 'text'
			)
		);
		
		return $settings_list;
	}
	
	/**
	 * Cart icons list
	 *
	 * @return array
	 */
	public function get_menu_icons() {
		$menu_icons_list = array(
			'zero' => '<i class="mprm-cart-font icon-mprm-cartzero"></i>',
			'1' => '<i class="mprm-cart-font icon-mprm-cart1"></i>',
			'2' => '<i class="mprm-cart-font icon-mprm-cart2"></i>',
			'3' => '<i class="mprm-cart-font icon-mprm-cart3"></i>',
			'4' => '<i class="mprm-cart-font icon-mprm-cart4"></i>',
			'5' => '<i class="mprm-cart-font icon-mprm-cart5"></i>',
			'6' => '<i class="mprm-cart-font icon-mprm-cart6"></i>',
			'7' => '<i class="mprm-cart-font icon-mprm-cart7"></i>',
			'8' => '<i class="mprm-cart-font icon-mprm-cart8"></i>',
			'9' => '<i class="mprm-cart-font icon-mprm-cart9"></i>',
			'10' => '<i class="mprm-cart-font icon-mprm-cart10"></i>'
		);
		
		return apply_filters('mprm-menu-cart-icons', $menu_icons_list);
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
		
		if ($tab && !empty($sections[ $tab ])) {
			$tabs = $sections[ $tab ];
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
				'main' => esc_html__('General', 'mprm-menu-cart'),
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

		if ( empty($mp_menu_options) ) {
			$mp_menu_options = array();
		}

		if ( empty($_POST[ '_wp_http_referer' ]) ) {
			return $input;
		}

		parse_str( sanitize_text_field( wp_unslash( $_POST[ '_wp_http_referer' ] ) ), $referrer );

		$settings = $this->get_registered_settings();
		
		$tab = isset($referrer[ 'tab' ]) ? $referrer[ 'tab' ] : 'general';
		
		$section = isset($referrer[ 'section' ]) ? $referrer[ 'section' ] : 'main';
		
		$input = $input ? $input : array();
		$input = apply_filters('mp_menu_settings_' . $tab . '-' . $section . '_sanitize', $input);
		
		if ('main' === $section) {
			// Check for extensions that aren't using new sections
			$input = apply_filters('mp_menu_settings_' . $tab . '_sanitize', $input);
		}
		
		// Loop through each setting being saved and pass it through a sanitization filter
		foreach ($input as $key => $value) {
			// Get the setting type (checkbox, select, etc)
			$type = isset($settings[ $tab ][ $key ][ 'type' ]) ? $settings[ $tab ][ $key ][ 'type' ] : false;
			
			if ($type) {
				// Field type specific filter
				$input[ $key ] = apply_filters('mp_menu_settings_sanitize_' . $type, $value, $key);
			}
			
			// General filter
			$input[ $key ] = apply_filters('mp_menu_settings_sanitize', $input[ $key ], $key);
		}
		
		// Loop through the whitelist and unset any that are empty for the tab being saved
		$main_settings = $section == 'main' ? $settings[ $tab ] : array(); // Check for extensions that aren't using new sections
		$section_settings = !empty($settings[ $tab ][ $section ]) ? $settings[ $tab ][ $section ] : array();
		$found_settings = array_merge($main_settings, $section_settings);
		
		if (!empty($found_settings)) {
			
			foreach ($found_settings as $key => $value) {
				// settings used to have numeric keys, now they have keys that match the option ID. This ensures both methods work
				if (is_numeric($key)) {
					$key = $value[ 'id' ];
				}
				if (empty($input[ $key ])) {
					unset($mp_menu_options[ $key ]);
				}
			}
		}
		// Merge our new settings with the existing
		$output = array_merge($mp_menu_options, $input);
		
		add_settings_error('mprm-notices', '', esc_html__('Settings updated.', 'mprm-menu-cart'), 'updated');
		
		return $output;
	}
	
	/**
	 * Options page
	 *
	 * Render settings page
	 */
	public function options_page() {

		$data[ 'settings_tabs' ] = $settings_tabs = $this->get_settings_tabs();
		$settings_tabs = empty($settings_tabs) ? array() : $settings_tabs;
		$key = 'main';

		$data[ 'active_tab' ] = isset($_GET[ 'tab' ]) && array_key_exists( sanitize_text_field( wp_unslash( $_GET[ 'tab' ] ) ), $settings_tabs) ?
			sanitize_text_field( wp_unslash( $_GET[ 'tab' ] ) ) : 'general';

		$data[ 'sections' ] = $this->get_settings_tab_sections($data[ 'active_tab' ]);
		$data[ 'section' ] = isset($_GET[ 'section' ]) && !empty($data[ 'sections' ]) && array_key_exists( sanitize_text_field( wp_unslash( $_GET[ 'section' ] ) ), $data[ 'sections' ]) ?
			sanitize_text_field( wp_unslash( $_GET[ 'section' ] ) ) : $key;

		echo View::get_instance()->get_template_html('settings', $data); // phpcs:ignore
	}
	
	/**
	 * Get settings tabs
	 *
	 * @return mixed
	 */
	public function get_settings_tabs() {
		$tabs = array();
		$tabs[ 'general' ] = esc_html__('General', 'mprm-menu-cart');
		
		return apply_filters('mp_menu_settings_tabs', $tabs);
	}
	
	/**
	 * @param string $key
	 * @param bool $default
	 *
	 * @return mixed
	 */
	public function get_option($key = '', $default = false) {
		if ($this->settings_type == 'extension') {
			return Main_Settings::get_instance()->get_option($key, $default);
		} elseif ($this->settings_type == 'extension') {
			global $mp_menu_options;
			
			if (empty($mp_menu_options)) {
				$mp_menu_options = $this->get_settings();
			}
			
			$value = !empty($mp_menu_options[ $key ]) ? $mp_menu_options[ $key ] : $default;
			$value = apply_filters('mp_menu_get_option', $value, $key, $default);
			
			return apply_filters('mp_menu_get_option_' . $key, $value, $key, $default);
		}
	}
}