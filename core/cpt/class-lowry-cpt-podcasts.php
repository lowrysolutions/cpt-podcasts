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
			'feeds' => false,
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
		
		//add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		
		//add_filter( 'manage_' . $this->post_type . '_posts_columns', array( $this, 'admin_column_add' ) );
		
		//add_action( 'manage_' . $this->post_type . '_posts_custom_column', array( $this, 'admin_column_display' ), 10, 2 );
		
	}
	
	/**
	 * Add Meta Box
	 * 
	 * @since 1.0.0
	 */
	public function add_meta_boxes() {
		
		add_meta_box(
			'podcast-download-url',
			sprintf( _x( '%s Meta', 'Metabox Title', 'lowry-cpt-podcasts' ), $this->label_singular ),
			array( $this, 'metabox_content' ),
			$this->post_type,
			'normal'
		);
		
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
				'description' => __( 'The URL to download this asset, or the landing page URL.', 'lowry-cpt-podcasts' ),
			)
		);
		
		rbm_do_field_text(
			'podcast_embed_code',
			_x( 'Podcast Embed Code', 'Podcast Embed Code Label', 'lowry-cpt-podcasts' ),
			false,
			array(
				'description' => __( 'For podcasts, add the Podcast Embed code here (e.g iframe)', 'lowry-cpt-podcasts' ),
			)
		);
		
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