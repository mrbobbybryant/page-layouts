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
namespace WPLAYOUTS\Templates;

function setup() {

//	$template_files->add_template( array( 'custom-layout-page.php' => 'Custom Page Layouts' ) );
	add_action( 'plugins_loaded', array( __NAMESPACE__ . '\Template_Files', 'init' ) );
}

class Template_Files {

	protected $template;

	public function __construct() {
		$this->template = array('custom-layout-page.php' => 'Custom Page Layouts');
	}

	public static function init() {
		$self = new self();
		add_filter( 'page_attributes_dropdown_pages_args', array( $self, 'register_template_files' ) );
		add_filter( 'wp_insert_post_data', array( $self, 'register_template_files' ) );
		add_filter( 'template_include', array( $self, 'load_registered_templates' ) );
	}

	public function register_template_files( $args ) {

		$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

		$templates = wp_get_theme()->get_page_templates();
		if ( empty( $templates ) ) {
			$templates = array();
		}

		// New cache, therefore remove the old one
		wp_cache_delete( $cache_key , 'themes');

		// Now add our template to the list of templates by merging our templates
		// with the existing templates array from the cache.
		$templates = array_merge( $templates, $this->template );

		// Add the modified cache to allow WordPress to pick it up for listing
		// available templates
		wp_cache_add( $cache_key, $templates, 'themes', 1800 );

		return $args;
	}

	public function load_registered_templates( $template ) {

		global $post;
		$current_template = get_post_meta( $post->ID, '_wp_page_template', true );

		if ( isset( $current_template ) ) {
			$template_exists = $this->template_exists( $current_template );
		}

		if ( false === $template_exists ) {
			return $template;
		}
		$something = basename( __DIR__ );
		$file = plugin_dir_path( __FILE__ ) . $current_template;
		$file = str_replace( $something, 'templates', $file );

		if ( file_exists( $file ) ) {
			return $file;
		}

		return $template;
	}

	public static function add_template( $template ) {
		$self = new self();
		$self->template[] = $template;
	}

	public function remove_template( $template_key ) {
		unset( $this->template[ $template_key ] );
	}

	private function template_exists( $template_name ) {
		return array_key_exists( $template_name, $this->template );
	}

	public static function get_plugin_part( $template, $require_once = false ) {
		$self = new self();
		$directory = WPLAYOUTS_PATH . 'templates/' . $template;
		if ( file_exists( $directory ) ) {
			$self->load_plugin_template( $directory, $require_once );
		}
	}

	private function load_plugin_template( $template_file, $require_once ) {
		if ( $require_once ) {
			require_once( $template_file );
		} else {
			require( $template_file );
		}
	}
}
