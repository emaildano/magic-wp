<?php

/**
 * Plugin Name:       Magic for WordPress
 * Plugin URI:        https://github.com/emaildano/magic-wp
 * Description:       Magic for WordPress provides Passwordless login for WordPress sites.
 * Version:           0.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Daniel Olson
 * Author URI:        https://github.com/emaildano
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       magic-wp
 * Domain Path:       /languages
 */

class Magic
{
  private $magic_options;

  public function __construct()
  {
    add_action('admin_menu', array($this, 'magic_add_plugin_page'));
    add_action('admin_init', array($this, 'magic_page_init'));
  }

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

  public function magic_create_admin_page()
  {
    $this->magic_options = get_option('magic_option_name'); ?>

    <div class="wrap">
      <h2>Magic</h2>
      <p></p>
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
}
if (is_admin())
  $magic = new Magic();

/* 
 * Retrieve this value with:
 * $magic_options = get_option( 'magic_option_name' ); // Array of All Options
 * $publishable_key_0 = $magic_options['publishable_key_0']; // Publishable Key
 */


wp_register_script('magic-sdk', 'https://cdn.jsdelivr.net/npm/magic-sdk@latest/dist/magic.js', array(), 'latest', true);


$magic_options = get_option('magic_option_name');
wp_localize_script('magic-sdk', 'magic_wp', $magic_options);

function magic_scripts()
{
  wp_enqueue_script('magic-sdk');
  wp_enqueue_script('magic-wp', plugin_dir_url(__FILE__) . 'main.js', array('magic-sdk',), false);
}

add_action('wp_enqueue_scripts', 'magic_scripts');
