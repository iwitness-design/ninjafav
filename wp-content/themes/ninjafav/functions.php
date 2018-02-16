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
	wp_enqueue_style( 'ninjafav-style', get_stylesheet_uri() );
}
add_action( 'wp_enqueue_scripts', 'ninjafav_stylesheet' );

add_filter( 'edd_default_downloads_name', function( $labels ) {
	$labels['singular'] = __( 'Product', 'ninjafav' );
	$labels['plural']   = __( 'Products', 'ninjafav' );

	return $labels;
} );

add_action( 'fes_payment_receipt_after_table', function( $payment ) {
	?>
		<h3><?php _e( 'Child Info', 'ninjafav' ); ?></h3>

	<table id="edd_purchase_receipt">

		<tbody>

		<tr>
			<td><strong><?php _e( 'Name', 'ninjafav' ); ?>:</strong></td>
			<td><?php echo esc_html( $payment->__get( 'child_name' ) ); ?></td>
		</tr>

		<tr>
			<td><strong><?php _e( 'Age', 'ninjafav' ); ?>:</strong></td>
			<td><?php echo esc_html( $payment->__get( 'child_age' ) ); ?></td>
		</tr>

		<tr>
			<td><strong><?php _e( 'Gym Name', 'ninjafav' ); ?>:</strong></td>
			<td><?php echo esc_html( $payment->__get( 'child_name_of_gym' ) ); ?></td>
		</tr>

		<tr>
			<td><strong><?php _e( 'Favorite Obstacle', 'ninjafav' ); ?>:</strong></td>
			<td><?php echo esc_html( $payment->__get( 'child_favorite_obstacle' ) ); ?></td>
		</tr>

		<tr>
			<td><strong><?php _e( 'Notes', 'ninjafav' ); ?>:</strong></td>
			<td><?php echo esc_html( $payment->__get( 'child_notes' ) ); ?></td>
		</tr>

		</tbody>
	</table>
	<?php
} );

add_filter( 'fes_vendor_dashboard_menu', function( $menu_items ) {
	unset( $menu_items['my_products'] );
	unset( $menu_items['orders'] );
	unset( $menu_items['profile'] );
	return $menu_items;
} );

add_filter( 'edd_purchase_download_form', function( $purchase_form, $args ) {
	return sprintf( '<a href="/checkout?edd_action=add_to_cart&download_id=%d" class="button edd-submit">Purchase</a>', $args['download_id'] );
}, 10, 2 );

add_action( 'edd_add_to_cart', function( $data ) {
	edd_empty_cart();
}, 9 );
