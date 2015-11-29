<?php
/**
 * Simple Google Maps
 * This class provides must of the core plugin functionality.
 *
 * @package Simple Google Maps
 * @since 0.1.0
 */

namespace SIMPLE_GOOGLE_MAPS\Metaboxes;
use SIMPLE_GOOGLE_MAPS\Country_Select as Select;
use SIMPLE_GOOGLE_MAPS\Helpers as Helpers;

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
		add_action( 'save_post_google_map', array( $this, 'save_custom_metabox_data' ) );
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

class Google_Map_Metabox extends Custom_metabox {

	/**
	 * Outputs custom metabox div container
	 * @param $post
	 */
	public function render_custom_metabox( $post ) {
		wp_nonce_field( SIMPLE_GOOGLE_MAPS_PATH, 'simple_google_map_nonce' );
		$meta_data = array_map( function( $a ){ return $a[0]; }, get_post_meta( $post->ID ) );
		?>
		<div id="google-map-input">
			<?php
			foreach( $this->meta_names as $key => $value ) {

				?>
				<div class="google-row">
					<label for="<?php echo str_replace( '_', '-', $key ) ?>"><?php echo $value ?></label>
					<input type="text" class="map-input" size="40" placeholder="<?php echo $value ?>" name="<?php echo $key ?>" id="<?php echo str_replace( '_', '-', $key ) ?>"
					       value="<?php if ( ! empty ( $meta_data[$key] ) ) {
						       echo esc_attr( $meta_data[$key] );
					       } ?>"/>
				</div>
				<?php
			}

			$country = Helpers\postmeta_value_exists( $meta_data, 'countries' );
			echo new Select\Country_Select( $country );

			?>
			<input id="geolat" name="geolat" class="text" type="hidden" Value="<?php if ( ! empty ( $meta_data['geolat'] ) ) {
				echo esc_attr( $meta_data['geolat'] );
			} ?>"/>
			<input id="geolng" name="geolng" class="text" type="hidden" Value="<?php if ( ! empty ( $meta_data['geolng'] ) ) {
				echo esc_attr( $meta_data['geolng'] );
			} ?>"/>
		</div>

		<div id="map"></div>
		<?php
	}

	public function save_custom_metabox_data( $post_id ) {

		if ( $is_autosave = wp_is_post_autosave( $post_id ) ) {
			return;
		}

		if ( $is_revision = wp_is_post_revision( $post_id ) ) {
			return;
		}

		$is_valid_nonce = ( isset( $_POST[ 'simple_google_map_nonce' ] ) && wp_verify_nonce( $_POST[ 'simple_google_map_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
		if ( ! $is_valid_nonce ) {
			return;
		}

		foreach ( $this->meta_names as $key => $value ) {
			if ( isset( $_POST[ $key ] ) ) {
				update_post_meta( $post_id, $key, sanitize_text_field( $_POST[ $key ] ) );
			}
		}

		if ( isset( $_POST[ 'countries' ] ) ) {
			update_post_meta( $post_id, 'countries', sanitize_text_field( $_POST[ 'countries' ] ) );
		}

		if ( isset( $_POST[ 'geolat' ] ) ) {
			update_post_meta( $post_id, 'geolat', filter_var( $_POST[ 'geolat' ], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) );
		}

		if ( isset( $_POST[ 'geolng' ] ) ) {
			update_post_meta( $post_id, 'geolng', filter_var( $_POST[ 'geolng' ], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) );
		}

	}
}
$meta_names = array(
		'address_1' => 'Address Line 1',
		'address_2' => 'Address Line 2',
		'city' => 'City',
		'state' => 'State/Province/Region',
		'zipcode' => 'Zip/ Postal Code'
);

$wp_custom_metaboxes = new Google_Map_Metabox( 'simple-maps', $meta_names );
$wp_custom_metaboxes->init();
$wp_custom_metaboxes->add_custom_metabox( 'add-google-map', 'Create Google Map', 'google_map' );
