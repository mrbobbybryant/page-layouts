<?php
/**
 * Summary (no period for file headers)
 *
 * Description. (use period)
 *
 * @link URL
 * @since x.x.x (if available)
 *
 * @package WordPress
 * @subpackage Component
 */

namespace WPLAYOUTS\scripts;

function setup() {
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\enqueue_admin_styles' );
	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_styles' );
}

function enqueue_admin_styles() {
	wp_enqueue_style(
		'page-layouts-admin',
		WPLAYOUTS_URL . 'assets/css/page_layouts-admin.css',
		array(),
		WPLAYOUTS_VERSION
	);
}

function enqueue_styles() {
	wp_enqueue_style(
			'page-layouts-css',
			WPLAYOUTS_URL . 'assets/css/page_layouts.css',
			array(),
			WPLAYOUTS_VERSION
	);
}
