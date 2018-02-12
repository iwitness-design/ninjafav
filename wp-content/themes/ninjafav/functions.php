<?php

// customize the EDD Slug
define('EDD_SLUG', 'ninjas');

/**
 * Enqueue default stylesheet
 *
 * @since  1.0.0
 *
 * @author Tanner Moushey
 */
function ninjafav_stylesheet() {
	wp_dequeue_style( 'checkout-style' );
	wp_enqueue_style( 'checkout-style', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'ninjafav_stylesheet' );