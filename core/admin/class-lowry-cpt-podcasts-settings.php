<?php
/**
 * Class Lowry_CPT_Podcasts_Settings
 *
 * Adds Settings for the Podcasts
 *
 * @since {{VERSION}}
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Lowry_CPT_Podcasts_Settings {

	/**
	 * GF_ActOn constructor.
	 *
	 * @since {{VERSION}}
	 */
	function __construct() {

		add_action( 'admin_init', array( $this, 'register_settings' ) );

		add_action( 'admin_menu', array( $this, 'submenu_page' ) );

	}

	/**
	 * Register Settings using the WP Settings API
	 * 
	 * @access		public
	 * @since		{{VERSION}}
	 * @return		void
	 */
	public function register_settings() {

		add_settings_section(
			'lowry_cpt_podcasts_settings_section',
			__return_null(),
			'__return_false()',
			'lowry-podcasts-settings'
		);

		$fields = $this->get_settings_fields();

		foreach ( $fields as $field ) {

			register_setting( 'lowry_cpt_podcasts_settings_section', '_rbm_' . $field['id'] );

		}

	}

	/**
	 * Add Settings Page for our Plugin
	 * 
	 * @access      public
	 * @since       {{VERSION}}
	 * @return      void
	 */
	public function submenu_page() {

		add_submenu_page(
			'options-general.php',
			_x( 'Lowry Podcast Settings', 'Admin Page Title', 'lowry-cpt-podcasts' ),
			_x( 'Podcast Settings', 'Admin Sub-Menu Title', 'lowry-cpt-podcasts' ),
			'manage_options',
			'lowry-podcasts-settings',
			array( $this, 'settings_page' )
		);

	}

	/**
	 * Output Fields on the Settings Page
	 * 
	 * @access		public
	 * @since		{{VERSION}}
	 * @return		void
	 */
	public function settings_page() {

		// Can't use Settings Sections properly with RBM Field Helpers :/
		?>

		<div class="wrap lowry-podcasts-settings-settings">
			<h1><?php echo get_admin_page_title(); ?></h1>

			<form action="options.php" method="post">

				<?php settings_fields( 'lowry_cpt_podcasts_settings_section' ); ?>

				<table class="form-table">

					<tbody>

						<?php 
							$fields = $this->get_settings_fields();
							foreach ( $fields as $field ) : 
								$value = get_option( '_rbm_' . $field['id'] );
								$value = ( $value ) ? $value : '';
							?>

							<tr<?php echo ( isset( $field['row_class'] ) ) ? ' class="' . $field['row_class'] . '"' : ''; ?>>

								<th scope="row">
									<label for="<?php echo $field['id']; ?>"><?php echo $field['label']; ?></label>
								</th>

								<td>
									<?php if ( $field['type'] == 'repeater' ) : ?>
										<?php call_user_func( 'rbm_do_field_' . $field['type'], $field['id'], false, $field['args']['fields'], $value ); ?>
									<?php else : ?>
										<?php call_user_func( 'rbm_do_field_' . $field['type'], $field['id'], false, $value, $field['args'] ); ?>
									<?php endif; ?>
								</td>

							</tr>

						<?php endforeach; ?>

					</tbody>

				</table>

				<?php submit_button(); ?>

			</form>

		</div>

		<?php

	}

	/**
	 * Returns Filterable Array of our Settings Fields
	 * 
	 * @access      private
	 * @since       {{VERSION}}
	 * @return      array Settings Field Parameters. See RBM Field Helpers
	 */
	private function get_settings_fields() {

		$fields = array(
			array(
				'id' => 'lowry_podcast_author',
				'type' => 'text',
				'label' => __( 'Podcast Author', 'lowry-cpt-podcasts' ),
				'args' => array(
					'input_class' => 'regular-text',
					'default' => 'Lowry Solutions',
				),
			),
			array(
				'id' => 'lowry_podcast_summary',
				'type' => 'wysiwyg',
				'label' => __( 'Podcast Summary', 'lowry-cpt-podcasts' ),
				'args' => array(
					'default' => 'Raising the Bar, brought to you by Lowry Solutions, the industry leader in AIDC, barcode and Blockchain technology, discusses the latest news and trends on barcode, RFID, Industrial Internet of Things (IioT), Managed Print Solutions, Enterprise Mobility, Blockchain and Industry 4.0. ',
				),
			),
			array(
				'id' => 'lowry_podcast_subtitle',
				'type' => 'wysiwyg',
				'label' => __( 'Podcast Subtitle', 'lowry-cpt-podcasts' ),
				'args' => array(
					'default' => '"Raising the Bar" brings the latest topics and discussions about barcode, RFID, IioT and Industry 4.0. ',
				),
			),
			array(
				'id' => 'lowry_podcast_owner_name',
				'type' => 'text',
				'label' => __( 'Podcast Owner Name', 'lowry-cpt-podcasts' ),
				'args' => array(
					'input_class' => 'regular-text',
					'default' => 'Lowry Solutions',
				),
			),
			array(
				'id' => 'lowry_podcast_owner_email',
				'type' => 'text',
				'label' => __( 'Podcast Owner Email', 'lowry-cpt-podcasts' ),
				'args' => array(
					'input_class' => 'regular-text',
					'default' => 'Market1@lowrysolutions.com',
				),
			),
			array(
				'id' => 'lowry_podcast_explicit',
				'type' => 'checkbox',
				'label' => __( 'Is Podcast Explicit?', 'lowry-cpt-podcasts' ),
			),
			array(
				'id' => 'lowry_podcast_keywords',
				'type' => 'text',
				'label' => __( 'Podcast Keywords', 'lowry-cpt-podcasts' ),
				'args' => array(
					'input_class' => 'regular-text',
					'description' => __( 'Separated by Commas', 'lowry-cpt-podcasts' ),
				),
			),
			array(
				'id' => 'lowry_podcast_image',
				'type' => 'image',
				'label' => __( 'Podcast Image', 'lowry-cpt-podcasts' ),
			),
			array(
				'id' => 'lowry_podcast_rating',
				'type' => 'text',
				'label' => __( 'Podcast Rating', 'lowry-cpt-podcasts' ),
				'args' => array(
					'input_class' => 'regular-text',
					'default' => 'TV-G',
				),
			),
			array(
				'id' => 'lowry_podcast_location',
				'type' => 'text',
				'label' => __( 'Podcast Location', 'lowry-cpt-podcasts' ),
				'args' => array(
					'input_class' => 'regular-text',
					'default' => 'Brighton, Michigan',
				),
			),
			array(
				'id' => 'lowry_podcast_frequency',
				'type' => 'text',
				'label' => __( 'Podcast Frequency', 'lowry-cpt-podcasts' ),
				'args' => array(
					'input_class' => 'regular-text',
					'default' => 'Monthly to Quarterly',
				),
			),
			array(
				'id' => 'lowry_podcast_category',
				'type' => 'text',
				'label' => __( 'Podcast Category', 'lowry-cpt-podcasts' ),
				'args' => array(
					'input_class' => 'regular-text',
					'default' => 'Technology',
				),
			),
		);

		return apply_filters( 'lowry_cpt_podcast_settings_fields', $fields );

	}

}

$instance = new Lowry_CPT_Podcasts_Settings();