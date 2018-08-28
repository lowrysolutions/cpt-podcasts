<?php
/**
 * Provides helper functions.
 *
 * @since	  1.0.0
 *
 * @package	Lowry_CPT_Podcasts
 * @subpackage Lowry_CPT_Podcasts/core
 */
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Returns the main plugin object
 *
 * @since		1.0.0
 *
 * @return		Lowry_CPT_Podcasts
 */
function LOWRYCPTPODCASTS() {
	return Lowry_CPT_Podcasts::instance();
}