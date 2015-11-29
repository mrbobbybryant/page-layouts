<?php
/**
 * Template Name: Custom Page Layouts
 */

use WPLAYOUTS\Views as Views;
$layout = get_post_meta( get_the_ID(), 'page_layout_selection', true );
get_header();

if ( ! empty( $layout ) ) {
	Views\render_selected_layout( $layout );
}

get_footer();

