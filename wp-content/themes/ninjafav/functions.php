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
			<td><strong><?php _e( 'Party Date', 'ninjafav' ); ?>:</strong></td>
			<td><?php echo esc_html( $payment->__get( 'child_party_date' ) ); ?></td>
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

add_filter( 'edd_payment_gateways', function( $gateways ) {
	$gateways['manual'] = array(
		'admin_label'    => __( 'Manual Payment', 'easy-digital-downloads' ),
		'checkout_label' => __( 'Manual Payment', 'easy-digital-downloads' )
	);

	return $gateways;
} );

add_filter( 'edd_enabled_payment_gateways', function( $gateways ) {
	if ( is_admin() ) {
		return $gateways;
	}

	if ( 1 == rcp_get_subscription_id() ) {
		unset( $gateways['stripe'] );
	} else {
		unset( $gateways['manual'] );
	}

	return $gateways;
} );

add_filter( 'eddc_commissions_calculated', function( $commission, $payment ) {
	switch( $payment->total ) {
		case '80':
			unset( $commission[0]['type'] );
			$commission[0]['rate'] = $commission[0]['commission_amount'] = 60;
			break;
		case '30':
			unset( $commission[0]['type'] );
			$commission[0]['rate'] = $commission[0]['commission_amount'] = 25;
			break;
		case '20':
			unset( $commission[0]['type'] );
			$commission[0]['rate'] = $commission[0]['commission_amount'] = 15;
			break;
	}

	return $commission;
}, 10, 2 );

add_filter( 'cfm_get_field_value_return_value_frontend', function( $value, $field, $payment_id, $user_id ) {
	if ( $value ) {
		return $value;
	}

	$meta = edd_get_payment_meta( $payment_id );

	switch( $field->id ) {
		case 'edd_first' :
			return $meta['user_info']['first_name'];
		case 'edd_last' :
			return $meta['user_info']['last_name'];
		case 'edd_email' :
			return $meta['email'];
	}

	return $value;
}, 10, 4 );

add_filter( 'eddc_email_template_tags', function( $tags ) {

	$tags[] = array(
		'tag'         => 'child_info',
		'description' => __( 'The child info associated with the order', 'ninjafav' ),
	);

	$tags[] = array(
		'tag'         => 'edit_link',
		'description' => __( 'The edit link associated with the order', 'ninjafav' ),
	);

	return $tags;
} );

add_filter( 'eddc_sale_alert_email', function( $message, $user_id, $commission_amount, $rate, $download_id, $commission_id ) {
	$commission = new EDD_Commission( $commission_id );

	if ( ! empty( $commission->payment_id ) ) {
		$payment = edd_get_payment( $commission->payment_id );
	} else {
		return $message;
	}

	ob_start();
	?>
		<?php _e( 'Name', 'ninjafav' ); ?>: <?php echo esc_html( get_post_meta( $payment->ID, 'child_name', true ) ); ?><br />
		<?php _e( 'Age', 'ninjafav' ); ?>: <?php echo esc_html( get_post_meta( $payment->ID, 'child_age', true ) ); ?><br />
		<?php _e( 'Party Date', 'ninjafav' ); ?>: <?php echo esc_html( get_post_meta( $payment->ID, 'child_party_date', true ) ); ?><br />
		<?php _e( 'Gym Name', 'ninjafav' ); ?>: <?php echo esc_html( get_post_meta( $payment->ID, 'child_name_of_gym', true ) ); ?><br />
		<?php _e( 'Favorite Obstacle', 'ninjafav' ); ?>: <?php echo esc_html( get_post_meta( $payment->ID, 'child_favorite_obstacle', true ) ); ?><br />
		<?php _e( 'Notes', 'ninjafav' ); ?>: <?php echo esc_html( get_post_meta( $payment->ID, 'child_notes', true ) ); ?>
	<?php

	$child_info = ob_get_clean();

	$args = array(
		'task' => 'edit-order',
		'order_id' => $payment->ID,
	);

	$link = add_query_arg( $args, get_home_url( null, 'ninja-dashboard' ) );

	$message = str_replace( '{child_info}', $child_info, $message );
	$message = str_replace( '{edit_link}', $link, $message );

	return $message;
}, 10, 6 );

add_filter( 'edd_login_redirect', function( $redirect, $user_id ) {
	if ( EDD_FES()->vendors->user_is_vendor( $user_id ) ) {
		return get_home_url( null, 'ninja-dashboard' );
	} else {
		return get_home_url();
	}
}, 10, 2 );