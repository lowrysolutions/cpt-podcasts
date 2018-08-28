<?php
/**
 * Plugin Name: CPT Podcasts
 * Plugin URI: https://github.com/lowrysolutions/cpt-podcasts
 * Description: Creates the "Podcasts" Custom Post Type
 * Version: 1.0.0
 * Text Domain: lowry-cpt-podcasts
 * Author: Eric Defore
 * Author URI: http://realbigmarketing.com/
 * Contributors: d4mation
 * GitHub Plugin URI: lowrysolutions/cpt-podcasts
 * GitHub Branch: develop
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Lowry_CPT_Podcasts' ) ) {

	/**
	 * Main Lowry_CPT_Podcasts class
	 *
	 * @since	  1.0.0
	 */
	class Lowry_CPT_Podcasts {
		
		/**
		 * @var			Lowry_CPT_Podcasts $plugin_data Holds Plugin Header Info
		 * @since		1.0.0
		 */
		public $plugin_data;
		
		/**
		 * @var			Lowry_CPT_Podcasts $admin_errors Stores all our Admin Errors to fire at once
		 * @since		1.0.0
		 */
		private $admin_errors;
		
		/**
		 * @var			Lowry_CPT_Podcasts $cpt Holds the CPT
		 * @since		1.0.0
		 */
		public $cpt;

		/**
		 * Get active instance
		 *
		 * @access	  public
		 * @since	  1.0.0
		 * @return	  object self::$instance The one true Lowry_CPT_Podcasts
		 */
		public static function instance() {
			
			static $instance = null;
			
			if ( null === $instance ) {
				$instance = new static();
			}
			
			return $instance;

		}
		
		protected function __construct() {
			
			$this->setup_constants();
			$this->load_textdomain();
			
			if ( version_compare( get_bloginfo( 'version' ), '4.4' ) < 0 ) {
				
				$this->admin_errors[] = sprintf( _x( '%s requires v%s of %s or higher to be installed!', 'Outdated Dependency Error', 'lowry-cpt-podcasts' ), '<strong>' . $this->plugin_data['Name'] . '</strong>', '4.4', '<a href="' . admin_url( 'update-core.php' ) . '"><strong>WordPress</strong></a>' );
				
				if ( ! has_action( 'admin_notices', array( $this, 'admin_errors' ) ) ) {
					add_action( 'admin_notices', array( $this, 'admin_errors' ) );
				}
				
				return false;
				
			}
			
			if ( ! class_exists( 'RBM_CPTS' ) ||
				! class_exists( 'RBM_FieldHelpers' ) ) {
				
				$this->admin_errors[] = sprintf( _x( 'To use the %s Plugin, both %s and %s must be active as either a Plugin or a Must Use Plugin!', 'Missing Dependency Error', 'lowry-cpt-podcasts' ), '<strong>' . $this->plugin_data['Name'] . '</strong>', '<a href="//github.com/realbig/rbm-field-helpers/" target="_blank">' . __( 'RBM Field Helpers', 'lowry-cpt-podcasts' ) . '</a>', '<a href="//github.com/realbig/rbm-cpts/" target="_blank">' . __( 'RBM Custom Post Types', 'lowry-cpt-podcasts' ) . '</a>' );
				
				if ( ! has_action( 'admin_notices', array( $this, 'admin_errors' ) ) ) {
					add_action( 'admin_notices', array( $this, 'admin_errors' ) );
				}
				
				return false;
				
			}
			
			$this->require_necessities();
			
			// Register our CSS/JS for the whole plugin
			add_action( 'init', array( $this, 'register_scripts' ) );
			
		}

		/**
		 * Setup plugin constants
		 *
		 * @access	  private
		 * @since	  1.0.0
		 * @return	  void
		 */
		private function setup_constants() {
			
			// WP Loads things so weird. I really want this function.
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . '/wp-admin/includes/plugin.php';
			}
			
			// Only call this once, accessible always
			$this->plugin_data = get_plugin_data( __FILE__ );

			if ( ! defined( 'Lowry_CPT_Podcasts_VER' ) ) {
				// Plugin version
				define( 'Lowry_CPT_Podcasts_VER', $this->plugin_data['Version'] );
			}

			if ( ! defined( 'Lowry_CPT_Podcasts_DIR' ) ) {
				// Plugin path
				define( 'Lowry_CPT_Podcasts_DIR', plugin_dir_path( __FILE__ ) );
			}

			if ( ! defined( 'Lowry_CPT_Podcasts_URL' ) ) {
				// Plugin URL
				define( 'Lowry_CPT_Podcasts_URL', plugin_dir_url( __FILE__ ) );
			}
			
			if ( ! defined( 'Lowry_CPT_Podcasts_FILE' ) ) {
				// Plugin File
				define( 'Lowry_CPT_Podcasts_FILE', __FILE__ );
			}

		}

		/**
		 * Internationalization
		 *
		 * @access	  private 
		 * @since	  1.0.0
		 * @return	  void
		 */
		private function load_textdomain() {

			// Set filter for language directory
			$lang_dir = Lowry_CPT_Podcasts_DIR . '/languages/';
			$lang_dir = apply_filters( 'lowry_cpt_podcasts_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), 'lowry-cpt-podcasts' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'lowry-cpt-podcasts', $locale );

			// Setup paths to current locale file
			$mofile_local   = $lang_dir . $mofile;
			$mofile_global  = WP_LANG_DIR . '/lowry-cpt-podcasts/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/lowry-cpt-podcasts/ folder
				// This way translations can be overridden via the Theme/Child Theme
				load_textdomain( 'lowry-cpt-podcasts', $mofile_global );
			}
			else if ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/lowry-cpt-podcasts/languages/ folder
				load_textdomain( 'lowry-cpt-podcasts', $mofile_local );
			}
			else {
				// Load the default language files
				load_plugin_textdomain( 'lowry-cpt-podcasts', false, $lang_dir );
			}

		}
		
		/**
		 * Include different aspects of the Plugin
		 * 
		 * @access	  private
		 * @since	  1.0.0
		 * @return	  void
		 */
		private function require_necessities() {
			
			require_once Lowry_CPT_Podcasts_DIR . 'core/cpt/class-lowry-cpt-podcasts.php';
			$this->cpt = new CPT_Lowry_Podcasts();
			
		}
		
		/**
		 * Show admin errors.
		 * 
		 * @access	  public
		 * @since	  1.0.0
		 * @return	  HTML
		 */
		public function admin_errors() {
			?>
			<div class="error">
				<?php foreach ( $this->admin_errors as $notice ) : ?>
					<p>
						<?php echo $notice; ?>
					</p>
				<?php endforeach; ?>
			</div>
			<?php
		}
		
		/**
		 * Register our CSS/JS to use later
		 * 
		 * @access	  public
		 * @since	  1.0.0
		 * @return	  void
		 */
		public function register_scripts() {
			
			wp_register_style(
				'lowry-cpt-podcasts',
				Lowry_CPT_Podcasts_URL . 'assets/css/style.css',
				null,
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : Lowry_CPT_Podcasts_VER
			);
			
			wp_register_script(
				'lowry-cpt-podcasts',
				Lowry_CPT_Podcasts_URL . 'assets/js/script.js',
				array( 'jquery' ),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : Lowry_CPT_Podcasts_VER,
				true
			);
			
			wp_localize_script( 
				'lowry-cpt-podcasts',
				'lowryCPTPodcasts',
				apply_filters( 'lowry_cpt_podcasts_localize_script', array() )
			);
			
			wp_register_style(
				'lowry-cpt-podcasts-admin',
				Lowry_CPT_Podcasts_URL . 'assets/css/admin.css',
				null,
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : Lowry_CPT_Podcasts_VER
			);
			
			wp_register_script(
				'lowry-cpt-podcasts-admin',
				Lowry_CPT_Podcasts_URL . 'assets/js/admin.js',
				array( 'jquery' ),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : Lowry_CPT_Podcasts_VER,
				true
			);
			
			wp_localize_script( 
				'lowry-cpt-podcasts-admin',
				'lowryCPTPodcasts',
				apply_filters( 'lowry_cpt_podcasts_localize_admin_script', array() )
			);
			
		}
		
	}
	
} // End Class Exists Check

/**
 * The main function responsible for returning the one true Lowry_CPT_Podcasts
 * instance to functions everywhere
 *
 * @since	  1.0.0
 * @return	  \Lowry_CPT_Podcasts The one true Lowry_CPT_Podcasts
 */
add_action( 'plugins_loaded', 'lowry_cpt_podcasts_load', 999 );
function lowry_cpt_podcasts_load() {

	require_once __DIR__ . '/core/lowry-cpt-podcasts-functions.php';
	LOWRYCPTPODCASTS();

}
