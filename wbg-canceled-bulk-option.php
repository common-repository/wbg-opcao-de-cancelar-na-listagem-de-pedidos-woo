<?php
/**
 * Plugin Name: WBG Opção de Cancelar na listagem de pedidos Woo
 * Plugin URI: https://www.webgoias.com.br/nossos-servicos/
 * Description: Adiciona opção para cancelar pedidos na Listagem 
 * Version: 1.1
 * Text Domain: wbg-canceled-bulk-option
 * Domain Path: /languages
 * License: GPLv3 or later
 * Author: Rodrigo Fleury Bastos
 * Author URI: http://www.webgoias.com.br
 */
 

##########


defined( 'ABSPATH' ) || exit;


add_filter( 'bulk_actions-edit-shop_order', 'wbg_add_cancel_bulk' ); 

function wbg_add_cancel_bulk( $bulk_actions ) {

$bulk_actions['wbg_mark_change_status_to_cancelled'] = 'Cancelar Pedido(s)';
return $bulk_actions;


}

add_action( 'admin_action_wbg_mark_change_status_to_cancelled', 'wbg_bulk_process_custom_status' ); // admin_action_{action name}

function wbg_bulk_process_custom_status() {

if( !isset( $_REQUEST['post'] ) && !is_array( $_REQUEST['post'] ) )
    return;

foreach( $_REQUEST['post'] as $order_id ) {

    $order = new WC_Order( $order_id );
    $order_note = 'A edição em massa gerou: ';
    $order->update_status( 'cancelled', $order_note, true );
}

$location = add_query_arg( array(
        'post_type' => 'shop_order',
		'wbg_mark_change_status_to_cancelled' => 1, // Setando a variavel = 1 para aparecer o notice
		'changed' => count( $_REQUEST['post'] ), // qtd de pedidos afetados
		'ids' => join( sanitize_key($_REQUEST['post']), ',' ),
		'post_status' => 'all'
	), 'edit.php' );

wp_redirect( admin_url( $location ) );
exit;


}

/*
 * Notices
 */
add_action('admin_notices', 'wbg_custom_order_status_notices');


function wbg_custom_order_status_notices() {

    global $pagenow, $typenow;


if( $typenow == 'shop_order' 
 && $pagenow == 'edit.php'
 && isset($_REQUEST['wbg_mark_change_status_to_cancelled'])
 && $_REQUEST['wbg_mark_change_status_to_cancelled'] == 1
 && isset($_REQUEST['changed'])) {

 echo "<div class=\"updated\"><p>".number_format_i18n(esc_html($_REQUEST['changed'])). " Pedidos tiveram status alterado para cancelado.</p></div>";

}


}

?>