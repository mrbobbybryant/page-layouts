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
use WPLAYOUTS\Views as Views;

$posts = Views\fetch_category_posts();

?>

<div id="layout-1" class="layout-1">
	<section id="hero">
		<div class="hero-section">
			<?php the_post_thumbnail( 'full', array( 'class' => 'header-image' ) ); ?>
			<h1 class="custom-page-title"><?php the_title(); ?></h1>
		</div>
	</section>

	<section class="featured">
		<?php for( $i = 0; $i <2; $i++ ) { ?>
			<div class="featured-excerpt id-<?php echo esc_attr( $posts[$i]['id'] ) ?>">
				<a href="<?php echo esc_attr( $posts[$i]['permalink'] ) ?>">
					<h3><?php esc_html_e( $posts[$i]['title'] ); ?></h3>
					<p class="author"><em><?php esc_html_e( $posts[$i]['author'] ); ?></em></p>
					<p><?php esc_html_e( $posts[$i]['excerpt'] ); ?></p>
				</a>
			</div>
		<?php } ?>
	</section>
	<section class="recent">
		<?php for( $i = 2; $i < count( $posts ); $i++ ) { ?>
			<div class="recent-excerpt id-<?php echo esc_attr( $posts[$i]['id'] ) ?>">
				<a href="<?php echo esc_attr( $posts[$i]['permalink'] ) ?>">
					<h4><?php esc_html_e( $posts[$i]['title'] ); ?></h4>
					<p class="author"><em><?php esc_html_e( $posts[$i]['author'] ); ?></em></p>
					<p><?php esc_html_e( $posts[$i]['excerpt'] ); ?></p>
				</a>
			</div>
		<?php } ?>
	</section>
</div>


<?php
