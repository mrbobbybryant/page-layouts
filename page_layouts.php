<?php
/**
 * Plugin Name: Page Layouts
 * Plugin URI:  http://wordpress.org/plugins
 * Description: The best WordPress extension ever made!
 * Version:     0.1.0
 * Author:      Bobby Bryant
 * Author URI:  
 * License:     GPLv2+
 * Text Domain: wplayouts
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2015 Bobby Bryant (email : bobby.bryant@10up.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Built using grunt-wp-plugin
 * Copyright (c) 2013 10up, LLC
 * https://github.com/10up/grunt-wp-plugin
 */

// Useful global constants
define( 'WPLAYOUTS_VERSION', '0.1.0' );
define( 'WPLAYOUTS_URL',     plugin_dir_url( __FILE__ ) );
define( 'WPLAYOUTS_PATH',    dirname( __FILE__ ) . '/' );

/**
 * Default initialization for the plugin:
 * - Registers the default textdomain.
 */
function wplayouts_init() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'wplayouts' );
	load_textdomain( 'wplayouts', WP_LANG_DIR . '/wplayouts/wplayouts-' . $locale . '.mo' );
	load_plugin_textdomain( 'wplayouts', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

/**
 * Activate the plugin
 */
function wplayouts_activate() {
	// First load the init scripts in case any rewrite functionality is being loaded
	wplayouts_init();

	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'wplayouts_activate' );

/**
 * Deactivate the plugin
 * Uninstall routines should be in uninstall.php
 */
function wplayouts_deactivate() {

}
register_deactivation_hook( __FILE__, 'wplayouts_deactivate' );

// Wireup actions
add_action( 'init', 'wplayouts_init' );
\AaronHolbrook\Autoload\autoload( __DIR__ . 'includes' );
// Wireup filters

// Wireup shortcodes
