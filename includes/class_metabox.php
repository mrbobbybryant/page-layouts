<?php
/**
 * Page Layouts
 * This class makes creating metaboxes faster and easier.
 *
 * @package Simple Google Maps
 * @since 0.1.0
 */

namespace WPLAYOUTS\Metaboxes;

function setup() {
	$wp_custom_metaboxes = new Page_layouts_Metabox(
			'wplayouts',
			array(
					array( 'key' => 'layout-1', 'name' => 'Layout One', 'img' => 'Layout_1.png' ),
					array( 'key' => 'layout-2', 'name' => 'Layout Two', 'img' => 'Layout_2.png' ),
					array( 'key' => 'layout-3', 'name' => 'Layout Three', 'img' => 'Layout_3.png' )
			)
	);
	$wp_custom_metaboxes->init();
	$wp_custom_metaboxes->add_custom_metabox( 'select-page-layout', 'Select Page Layout', 'page' );
}
class Custom_Metabox {

	/**
	 * @var string plugin text domain
	 */
	protected $textdomain;

	/**
	 * @var array Collection of custom metaboxes to register
	 */
	protected $custom_metaboxes;

	/**
	 * @var array List of post types using the custom metabox
	 */
	protected $post_types;

	protected $meta_names;

	function __construct( $textdomain, $meta_names ) {
		$this->textdomain = $textdomain;
		$this->custom_metaboxes = array();
		$this->post_types = array();
		$this->meta_names = $meta_names;
	}

	/**
	 * Bootstrap Class.
	 */
	public function init() {
		add_action( 'add_meta_boxes', array( $this, 'register_custom_metabox' ) );
		add_action( 'save_post_page', array( $this, 'save_custom_metabox_data' ) );
	}

	/**
	 * Loops through array of metaboxes and registers them with WordPress
	 */
	public function register_custom_metabox() {
		foreach( $this->custom_metaboxes as $metabox => $setting ) {
			add_meta_box(
				$setting['id'],
				$setting['name'],
				array( $this, 'render_custom_metabox' ),
				$setting['post_type'],
				$setting['context'],
				$setting['priority']
			);
		}
	}

	/**
	 * Checks to see if a certain post type already exists in $post_types, and
	 * if not we add it to the array.
	 * @param $post_type string
	 */
	public function add_post_type_support( $post_type ) {
		if ( is_array( $post_type ) ) {
			foreach ( $post_type as $type ) {
				if( ! in_array( $type, $this->post_types ) ) {
					$this->post_types[] = $type;
				}
			}
		}
		else {
			$this->post_types[] = $post_type;
		}

	}

	/**
	 * Adds an entry to the custom metaboxes array for metabox creation.
	 * Also calls add_post_type_support, to log required post types.
	 * @param $id string - computer readable
	 * @param $name string - Name for your metabox
	 * @param array $post_type - List of post types this metabox should display on
	 * @param string $context
	 * @param string $priority
	 *
	 * @return array
	 */
	public function add_custom_metabox( $id, $name, $post_type, $context = 'advanced', $priority = 'default' ) {

		$this->add_post_type_support( $post_type );

		return $this->custom_metaboxes[ $id ] = array(
			'id' => $id,
			'name' => $name,
			'post_type' => $post_type,
			'context' => $context,
			'priority' => $priority
		);
	}

	/**
	 * Checks if the current post type has a custom metabox.
	 * Returns true if it does.
	 * @return bool
	 */
	public function has_custom_metabox() {
		$post_type = get_post_type();

		if ( in_array( $post_type, $this->post_types ) ) {
			return true;
		}
		return false;
	}
}

class Page_layouts_Metabox extends Custom_metabox {

	/**
	 * Outputs custom metabox div container
	 * @param $post
	 */
	public function render_custom_metabox( $post ) {
		wp_nonce_field( WPLAYOUTS_PATH, 'page_layout_nonce' );
		$selection = get_post_meta( $post->ID, 'page_layout_selection', true );

		foreach ( $this->meta_names as $name ) {
			?>
			<label>
				<input
						type="radio"
						name="page_layout_selection"
						value="<?php echo esc_attr( $name['key'] ); ?>"
						<?php checked( $selection, $name['key'] ); ?>
				/>
				<img src="<?php echo esc_url( WPLAYOUTS_URL . 'images/src/' . $name['img'] ); ?>" class="layout-img" alt="<?php echo esc_attr( $name['name'] ); ?>">
			</label>


			<?php
		}


	}

	public function save_custom_metabox_data( $post_id ) {

		if ( $is_autosave = wp_is_post_autosave( $post_id ) ) {
			return;
		}

		if ( $is_revision = wp_is_post_revision( $post_id ) ) {
			return;
		}

		$is_valid_nonce = ( isset( $_POST[ 'page_layout_nonce' ] ) && wp_verify_nonce( $_POST[ 'page_layout_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
		if ( ! $is_valid_nonce ) {
			return;
		}

		if ( isset( $_POST[ 'page_layout_selection' ] ) ) {
			update_post_meta( $post_id, 'page_layout_selection', sanitize_text_field( $_POST[ 'page_layout_selection' ] ) );
		}

	}
}
