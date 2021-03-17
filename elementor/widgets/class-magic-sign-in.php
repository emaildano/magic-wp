<?php

/**
 * Magic config class.
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

namespace Magic\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

// Security Note: Blocks direct access to the plugin PHP files.
defined('ABSPATH') || die();

/**
 * Magic widget class.
 *
 * @since 0.0.0
 */
class MagicSignIn extends Widget_Base
{
	/**
	 * Class constructor.
	 *
	 * @param array $data Widget data.
	 * @param array $args Widget arguments.
	 */
	public function __construct($data = array(), $args = null)
	{
		parent::__construct($data, $args);

		// Load Magic SDK.
		wp_register_script('magic-sdk', 'https://cdn.jsdelivr.net/npm/magic-sdk@latest/dist/magic.js', array(), 'latest', true);
		wp_enqueue_script('magic-sdk');

		// Load Magic for WordPress Scripts.
		wp_register_script('magic-wp-plugin', plugin_dir_url(__FILE__) . 'main.js', array('magic-sdk',), false);
		wp_enqueue_script('magic-wp-plugin');

		$magic_options = get_option('magic_option_name');
		wp_localize_script('magic-sdk', 'magic_wp', $magic_options);

		$config = array(
			'templates' => [
				'unauthorized' => '<div class="magic-sign-in">
				<form onsubmit="handleLogin(event)">
		<input type="email" name="email" required="required" placeholder="Enter your email" />
		<button type="submit">Sign-in</button>
	</form></div>',
				'authorized' => '<p>Current user: <span data-magic-meta="user-email">${magic.user.email}</span></p>
<button onclick="handleLogout()">Logout</button>',
			]
		);

		wp_localize_script('magic-wp-plugin', 'settings', $config);
	}

	/**
	 * Retrieve the widget name.
	 *
	 * @since 0.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name()
	{
		return 'magic-wp-plugin';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords()
	{
		return ['magic', 'auth', 'form'];
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 0.0.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title()
	{
		return __('Magic Sign-in', 'magic-wp-plugin');
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @since 0.0.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon()
	{
		return 'fa fa-magic';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since 0.0.0
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories()
	{
		return array('general');
	}

	/**
	 * Enqueue scripts.
	 */
	public function get_script_depends()
	{
		return array('magic-wp-plugin');
	}

	/**
	 * Enqueue styles.
	 */
	public function get_style_depends()
	{
		return array('magic-wp-plugin');
	}


	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 0.0.0
	 *
	 * @access protected
	 */
	protected function _register_controls()
	{
		// Controls.
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 0.0.0
	 *
	 * @access protected
	 */
	protected function render()
	{
?>

		<div id="magic-sign-in"></div>

<?php }

	/**
	 * Render the widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 0.0.0
	 *
	 * @access protected
	 */
	protected function content_template()
	{
		//
	}
}
