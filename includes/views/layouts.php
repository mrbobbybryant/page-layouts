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

namespace WPLAYOUTS\Views;
use WPLAYOUTS\Templates as Templates;

function render_selected_layout( $layout ) {
	if ( empty( $layout ) ) {
		return;
	}

	switch( $layout ) {
		case 'layout-1':
			Templates\Template_Files::get_plugin_part( 'layout-1.php' );
			break;
		case 'layout-2':
			Templates\Template_Files::get_plugin_part( 'layout-2.php' );
			break;
		case 'layout-3':
			Templates\Template_Files::get_plugin_part( 'layout-3.php' );
			break;
		default:
			return;
	}
}

function fetch_category_posts() {
	$layout_postdata = array();

	$args = array(
		'post_type' => 'post',
		'post_status' => 'publish',
		'posts_per_page' => 5,
		'category_name' => 'cat-a',
		'no_found_rows' => true,
	);
	$layout_posts = new \WP_Query( $args );

	if ( $layout_posts->have_posts() ):
		while ( $layout_posts->have_posts() ) : $layout_posts->the_post();
			$author = get_userdata( get_the_author_meta('ID') );

			$layout_postdata[] = array(
					'id'           => get_the_ID(),
					'title'        => get_the_title(),
					'permalink'    => get_permalink(),
					'author'       => $author->data->user_nicename,
					'date'         => get_the_date( 'Y-m-d H:i:s' ),
					'post_content' => apply_filters( 'the_content', get_the_content() ),
					'excerpt'      => wp_trim_words( get_the_excerpt(), 30 )
			);


		endwhile;
	endif;
	wp_reset_query();

	return $layout_postdata;


}