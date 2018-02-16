<?php

$vendor_announcement = EDD_FES()->helper->get_option( 'fes-dashboard-notification', '' );
if ( $vendor_announcement ) : ?>
	<div id="fes-vendor-announcements">
		<?php echo apply_filters( 'fes_dashboard_content', do_shortcode( $vendor_announcement ) ); ?>
	</div>
<?php endif; ?>

<?php
global $orders;

if ( ! EDD_FES()->vendors->vendor_can_view_orders() ) {
	EDD_FES()->templates->fes_get_template_part( 'frontend', 'dashboard' );
	return;
}

global $orders;
$orders = EDD_FES()->vendors->get_all_orders( get_current_user_id(), array() );

do_action( 'fes_before_frontend_orders' );
?>
<h3 class="fes-headers" id="fes-orders-page-title"><?php _e( 'Orders', 'edd_fes' ); ?></h3>

<table class="table fes-table table-condensed  table-striped" id="fes-order-list">
	<thead>
		<tr>
			<th><?php _e( 'Order', 'edd_fes' ); ?></th>
			<th><?php _e( 'Fulfilled?', 'edd_fes' ); ?></th>
			<th><?php _e( 'Customer', 'edd_fes' ) ?></th>
			<th><?php _e( 'View Order','edd_fes' ) ?></th>
			<?php do_action( 'fes-order-table-column-title' ); ?>
			<th><?php _e( 'Date', 'edd_fes' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		if ( ! empty( $orders ) && count( $orders ) > 0 && is_array( $orders ) ) {
			foreach ( $orders as $order ) :
				if ( ! is_object( $order ) ) {
					continue;
				}
				?>
				<tr>
					<td class = "fes-order-list-td"><?php echo EDD_FES()->dashboard->order_list_title( $order->ID ); ?></td>
					<td class = "fes-order-list-td"><?php echo edd_custom_deliverables_fulfilled_column_value( '', $order->ID, 'eddcd_fulfilled' ); ?></td>
					<td class = "fes-order-list-td"><?php echo EDD_FES()->dashboard->order_list_customer( $order->ID ); ?></td>
					<td class = "fes-order-list-td"><?php EDD_FES()->dashboard->order_list_actions( $order->ID ); ?></td>
					<?php do_action( 'fes-order-table-column-value', $order ); ?>
					<td class = "fes-order-list-td"><?php echo EDD_FES()->dashboard->order_list_date( $order->ID ); ?></td>
				</tr>
				<?php
				do_action( 'fes_frontend_order_rows' );
			endforeach;
		} else {
			echo '<tr><td colspan="6">' . __( 'No orders found','edd_fes' ) . '</td></tr>';
		}
		?>
	</tbody>
</table>
<?php EDD_FES()->dashboard->order_list_pagination();

do_action( 'fes_after_frontend_orders' );
