<?php

/**
 * Magic class.
 *
 * @category   Class
 * @package    Magic
 * @subpackage WordPress
 * @author     DigitalCube <info@digitalcube.jp>
 * @license    https://opensource.org/licenses/GPL-3.0 GPL-3.0-only
 * @link       link(https://github.com/digitalcube/magic-wp-plugin, Magic for WordPress)
 * @since      0.0.0
 * php version 7.3.9
 */

if (!defined('ABSPATH')) {
	// Exit if accessed directly.
	exit;
}

/**
 * Main Magic Class
 *
 * The init class that runs the Magic plugin.
 * Intended To make sure that the plugin's minimum requirements are met.
 */
final class Magic
{

	/**
	 * Plugin Version
	 *
	 * @since 0.0.0
	 * @var string The plugin version.
	 */
	const VERSION = '0.0.0';

	/**
	 * Minimum Elementor Version
	 *
	 * @since 0.0.0
	 * @var string Minimum Elementor version required to run the plugin.
	 */
	const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

	/**
	 * Minimum PHP Version
	 *
	 * @since 0.0.0
	 * @var string Minimum PHP version required to run the plugin.
	 */
	const MINIMUM_PHP_VERSION = '7.0';

	/**
	 * Constructor
	 *
	 * @since 0.0.0
	 * @access public
	 */
	public function __construct()
	{
		// Load the translation.
		add_action('init', array($this, 'i18n'));

		// Initialize the plugin.
		add_action('plugins_loaded', array($this, 'init'));

		// Load options template.
		add_action('admin_menu', array($this, 'magic_add_plugin_page'));

		// Initialize options page.
		add_action('admin_init', array($this, 'magic_page_init'));
	}

	/**
	 * Add options page menu.
	 *
	 * @since 0.0.0
	 * @access public
	 */
	public function magic_add_plugin_page()
	{
		add_options_page(
			'Magic', // page_title
			'Magic', // menu_title
			'manage_options', // capability
			'magic', // menu_slug
			array($this, 'magic_create_admin_page') // function
		);
	}

	/**
	 * Register settings.
	 *
	 * @since 0.0.0
	 * @access public
	 */
	public function magic_page_init()
	{
		register_setting(
			'magic_option_group', // option_group
			'magic_option_name', // option_name
			array($this, 'magic_sanitize') // sanitize_callback
		);

		add_settings_section(
			'magic_setting_section', // id
			'Settings', // title
			array($this, 'magic_section_info'), // callback
			'magic-admin' // page
		);

		add_settings_field(
			'publishable_key_0', // id
			'Publishable Key', // title
			array($this, 'publishable_key_0_callback'), // callback
			'magic-admin', // page
			'magic_setting_section' // section
		);

		add_settings_field(
			'redirect_uri_0', // id
			'Redirect URI', // title
			array($this, 'redirect_uri_0_callback'), // callback
			'magic-admin', // page
			'magic_setting_section' // section
		);
	}

	public function magic_sanitize($input)
	{
		$sanitary_values = array();
		if (isset($input['publishable_key_0'])) {
			$sanitary_values['publishable_key_0'] = sanitize_text_field($input['publishable_key_0']);
		}

		if (isset($input['redirect_uri_0'])) {
			$sanitary_values['redirect_uri_0'] = sanitize_text_field($input['redirect_uri_0']);
		}

		return $sanitary_values;
	}

	public function magic_section_info()
	{
	}

	public function publishable_key_0_callback()
	{
		printf(
			'<input class="regular-text" type="text" name="magic_option_name[publishable_key_0]" id="publishable_key_0" value="%s">',
			isset($this->magic_options['publishable_key_0']) ? esc_attr($this->magic_options['publishable_key_0']) : ''
		);
	}

	public function redirect_uri_0_callback()
	{
		printf(
			'<input class="regular-text" type="text" name="magic_option_name[redirect_uri_0]" id="redirect_uri_0" value="%s">',
			isset($this->magic_options['redirect_uri_0']) ? esc_attr($this->magic_options['redirect_uri_0']) : ''
		);
	}

	/**
	 * Options page template.
	 *
	 * @since 0.0.0
	 * @access public
	 */
	public function magic_create_admin_page()
	{
		$this->magic_options = get_option('magic_option_name'); ?>

		<div class="wrap">
			<h2>Magic</h2>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
				settings_fields('magic_option_group');
				do_settings_sections('magic-admin');
				submit_button();
				?>
			</form>
		</div>
<?php }

	/**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 * Fired by `init` action hook.
	 *
	 * @since 0.0.0
	 * @access public
	 */
	public function i18n()
	{
		load_plugin_textdomain('magic-wp-plugin');
	}

	/**
	 * Initialize the plugin
	 *
	 * Validates that Elementor is already loaded.
	 * Checks for basic plugin requirements, if one check fail don't continue,
	 * if all check have passed include the plugin class.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 0.0.0
	 * @access public
	 */
	public function init()
	{

		// Check if Elementor installed and activated.
		if (!did_action('elementor/loaded')) {
			add_action('admin_notices', array($this, 'admin_notice_missing_main_plugin'));
			return;
		}

		// Check for required Elementor version.
		if (!version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=')) {
			add_action('admin_notices', array($this, 'admin_notice_minimum_elementor_version'));
			return;
		}

		// Check for required PHP version.
		if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
			add_action('admin_notices', array($this, 'admin_notice_minimum_php_version'));
			return;
		}

		// Once we get here, We have passed all validation checks so we can safely include our widgets.
		require_once 'class-widgets.php';
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have Elementor installed or activated.
	 *
	 * @since 0.0.0
	 * @access public
	 */
	public function admin_notice_missing_main_plugin()
	{
		deactivate_plugins(plugin_basename(ALGOLIA));

		return sprintf(
			wp_kses(
				'<div class="notice notice-warning is-dismissible"><p><strong>"%1$s"</strong> requires <strong>"%2$s"</strong> to be installed and activated.</p></div>',
				array(
					'div' => array(
						'class'  => array(),
						'p'      => array(),
						'strong' => array(),
					),
				)
			),
			'Magic',
			'Elementor'
		);
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required Elementor version.
	 *
	 * @since 0.0.0
	 * @access public
	 */
	public function admin_notice_minimum_elementor_version()
	{
		deactivate_plugins(plugin_basename(ALGOLIA));

		return sprintf(
			wp_kses(
				'<div class="notice notice-warning is-dismissible"><p><strong>"%1$s"</strong> requires <strong>"%2$s"</strong> version %3$s or greater.</p></div>',
				array(
					'div' => array(
						'class'  => array(),
						'p'      => array(),
						'strong' => array(),
					),
				)
			),
			'Magic',
			'Elementor',
			self::MINIMUM_ELEMENTOR_VERSION
		);
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 0.0.0
	 * @access public
	 */
	public function admin_notice_minimum_php_version()
	{
		deactivate_plugins(plugin_basename(ALGOLIA));

		return sprintf(
			wp_kses(
				'<div class="notice notice-warning is-dismissible"><p><strong>"%1$s"</strong> requires <strong>"%2$s"</strong> version %3$s or greater.</p></div>',
				array(
					'div' => array(
						'class'  => array(),
						'p'      => array(),
						'strong' => array(),
					),
				)
			),
			'Magic',
			'Elementor',
			self::MINIMUM_ELEMENTOR_VERSION
		);
	}
}

// Instantiate Magic.
new Magic();
