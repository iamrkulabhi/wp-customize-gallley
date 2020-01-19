<?php

/**
 * Plugin Name:       WP Customize Gallery
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       new wordpress customize gallery.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Rahul Kulabhi
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wp-customize-gallery
 * Domain Path:       /languages
 */

if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}


if ( is_admin() ) {
	require_once( 'classes/class-options.php' );
	require_once( 'classes/class-gallery.php' );
}

