<?php
/**
 * Class CPT_Lowry_Podcasts
 *
 * Creates the post type.
 *
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class CPT_Lowry_Podcasts extends RBM_CPT {

	public $post_type = 'podcast';
	public $label_singular = null;
	public $label_plural = null;
	public $labels = array();
	public $icon = 'video-alt2';
	public $post_args = array(
		'hierarchical' => true,
		'supports' => array( 'title', 'editor', 'author', 'thumbnail' ),
		'has_archive' => true,
		'rewrite' => array(
			'slug' => 'podcast',
			'with_front' => false,
			'feeds' => true,
			'pages' => true
		),
		'menu_position' => 11,
		'capability_type' => 'podcast',
	);

	/**
	 * CPT_Lowry_Podcasts constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {

		// This allows us to Localize the Labels
		$this->label_singular = __( 'Podcast', 'lowry-cpt-podcasts' );
		$this->label_plural = __( 'Podcasts', 'lowry-cpt-podcasts' );

		$this->labels = array(
			'menu_name' => __( 'Podcasts', 'lowry-cpt-podcasts' ),
			'all_items' => __( 'All Podcasts', 'lowry-cpt-podcasts' ),
		);

		parent::__construct();
		
		add_action( 'wp_head', array( $this, 'show_rss_feed' ) );
		
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		
		add_action( 'rss2_head', array( $this, 'add_to_rss_channel' ) );
		
		add_action( 'rss2_item', array( $this, 'add_to_rss_item' ) );
		
		add_filter( 'the_guid', array( $this, 'the_guid' ), 10, 2 );
		
		//add_filter( 'manage_' . $this->post_type . '_posts_columns', array( $this, 'admin_column_add' ) );
		
		//add_action( 'manage_' . $this->post_type . '_posts_custom_column', array( $this, 'admin_column_display' ), 10, 2 );
		
	}
	
	public function show_rss_feed() {
		
		$feed = get_post_type_archive_feed_link( $this->post_type );
        if ( $feed === '' || !is_string( $feed ) ) {
            $feed =  get_bloginfo( 'rss2_url' ) . "?post_type=$this->post_type";
        }
		
        printf( '<link rel="%1$s" type="%2$s" title="%3$s" href="%4$s" />', "alternate", "application/rss+xml", get_bloginfo( 'title' ) . ' &raquo; ' . $this->label_singular, $feed );
		
	}
	
	/**
	 * Add Meta Box
	 * 
	 * @since 1.0.0
	 */
	public function add_meta_boxes() {
		
		global $post;
		
		add_meta_box(
			'podcast-meta',
			sprintf( _x( '%s Meta', 'Metabox Title', 'lowry-cpt-podcasts' ), $this->label_singular ),
			array( $this, 'metabox_content' ),
			$this->post_type,
			'normal'
		);
		
		// We only want tags
		remove_meta_box( 'resource-categorydiv', $this->post_type, 'side' );
		
	}
	
	/**
	 * Add Meta Field
	 * 
	 * @since 1.0.0
	 */
	public function metabox_content() {
		
		rbm_do_field_text(
			'podcast_url',
			_x( 'Podcast URL', 'Podcast URL Label', 'lowry-cpt-podcasts' ),
			false,
			array(
				'description' => __( 'The Audio URL for the Podcast.', 'lowry-cpt-podcasts' ),
			)
		);
		
		rbm_do_field_text(
			'podcast_duration',
			_x( 'Podcast Duration', 'Podcast Duration Label', 'lowry-cpt-podcasts' ),
			false,
			array(
				'description' => __( 'The duration of the the Podcast. Example: 19:07', 'lowry-cpt-podcasts' ),
			)
		);
		
	}
	
	/**
	 * Add information for the RSS Series
	 * 
	 * @access		public
	 * @since		{{VERSION}}
	 * @return		void
	 */
	public function add_to_rss_channel() {
		
		if ( get_post_type() !== $this->post_type ) return;
		
		$podcast_image_id = get_option( '_rbm_lowry_podcast_image' );
		
		$podcast_image_url = '';
		if ( $podcast_image_id ) {
			$podcast_image_url = wp_get_attachment_image_url( $podcast_image_id );
		}
			
		?>

			<itunes:author><?php echo get_option( '_rbm_lowry_podcast_author', 'Lowry Solutions' ); ?></itunes:author>
			<itunes:summary>
				<?php echo get_option( '_rbm_lowry_podcast_summary', 'Raising the Bar, brought to you by Lowry Solutions, the industry leader in AIDC, barcode and Blockchain technology, discusses the latest news and trends on barcode, RFID, Industrial Internet of Things (IioT), Managed Print Solutions, Enterprise Mobility, Blockchain and Industry 4.0. ' ); ?>
			</itunes:summary>
			<itunes:subtitle><?php echo get_option( '_rbm_lowry_podcast_subtitle', '"Raising the Bar" brings the latest topics and discussions about barcode, RFID, IioT and Industry 4.0. ' ); ?></itunes:subtitle>
			<itunes:owner>
				<itunes:name><?php echo get_option( '_rbm_lowry_podcast_owner_name', 'Lowry Solutions' ); ?></itunes:name>
				<itunes:email><?php echo get_option( '_rbm_lowry_podcast_owner_email', 'Market1@lowrysolutions.com' ); ?></itunes:email>
			</itunes:owner>
			<itunes:explicit><?php echo ( get_option( '_rbm_lowry_podcast_explicit', false ) ) ? 'Yes' : 'No'; ?></itunes:explicit>
			<itunes:keywords>
				<?php echo trim( get_option( '_rbm_lowry_podcast_keywords', '' ) ); ?>
			</itunes:keywords>
			<itunes:image href="<?php echo ( $podcast_image_url ) ? convert_chars( $podcast_image_url ) : convert_chars( get_site_icon_url( 32 ) ); ?>"/>
			<rawvoice:rating><?php echo get_option( '_rbm_lowry_podcast_rating', 'TV-G' ); ?></rawvoice:rating>
			<rawvoice:location><?php echo get_option( '_rbm_lowry_podcast_location', 'Brighton, Michigan' ); ?></rawvoice:location>
			<rawvoice:frequency><?php echo get_option( '_rbm_lowry_podcast_frequency', 'Monthly to Quarterly' ); ?></rawvoice:frequency>
			<itunes:category text="<?php echo get_option( '_rbm_lowry_podcast_category', 'Technoloy' ); ?>"/>

		<?php
		
	}
	
	/**
	 * Add stuff to the inidividual RSS Item
	 * 
	 * @access		public
	 * @since		{{VERSION}}
	 * @return		void
	 */
	public function add_to_rss_item() {
		
		if ( get_post_type() !== $this->post_type ) return;
		
		$podcast_image_id = get_option( '_rbm_lowry_podcast_image' );
		
		$podcast_image_url = '';
		if ( $podcast_image_id ) {
			$podcast_image_url = wp_get_attachment_image_url( $podcast_image_id );
		}
			
		?>

			<?php if ( $duration = rbm_get_field( 'podcast_duration', get_the_ID() ) ) : ?>
				<itunes:duration><?php echo $duration; ?></itunes:duration>
			<?php endif; ?>
			<itunes:summary>
				<?php echo get_the_content_feed( 'rss2' ); ?>
			</itunes:summary>
			<itunes:image href="<?php echo ( $podcast_image_url ) ? convert_chars( $podcast_image_url ) : convert_chars( get_site_icon_url( 32 ) ); ?>"/>
			<itunes:keywords>
				<?php echo strip_tags( get_the_term_list( get_the_ID(), 'resource-tag', '', ',', '' ) ); ?>
			</itunes:keywords>
			<itunes:explicit><?php echo ( get_option( '_rbm_lowry_podcast_explicit', false ) ) ? 'Yes' : 'No'; ?></itunes:explicit>

		<?php
		
	}
	
	/**
	 * Make RSS Item link to the MP3
	 * 
	 * @param		string  $guid    GUID
	 * @param		integer $post_id Post ID
	 *                               
	 * @access		public
	 * @since		{{VERSION}}
	 * @return		string  GUID
	 */
	public function the_guid( $guid, $post_id ) {
		
		global $wp_query;
		
		if ( get_post_type( $post_id ) !== $this->post_type ) return $guid;
		
		return ( $podcast_url = rbm_get_field( 'podcast_url', $post_id ) ) ? $podcast_url : $guid;
		
	}
	
	/**
	 * Adds an Admin Column
	 * @param  array $columns Array of Admin Columns
	 * @return array Modified Admin Column Array
	 */
	public function admin_column_add( $columns ) {
		
		$columns['podcast_url'] = _x( 'Podcast URL', 'Podcast URL Admin Column Label', 'lowry-cpt-podcasts' );
		
		return $columns;
		
	}
	
	/**
	 * Displays data within Admin Columns
	 * @param string $column  Admin Column ID
	 * @param integer $post_id Post ID
	 */
	public function admin_column_display( $column, $post_id ) {
		
		switch ( $column ) {
				
			case 'podcast_url' :
				echo rbm_field( $column, $post_id );
				break;
				
		}
		
	}
	
}