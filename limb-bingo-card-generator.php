<?php
/**
 * Plugin Name: UBUD Bingo Cards Generator
 * Description: Generate, share or print bingo cards
 * Version: 1.4.1
 * Author: UBUD
 * License: GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
add_action( 'init', 'run_lbcg' );
function run_lbcg() {
	$plugin_dir = plugin_dir_path( __FILE__ );
	$plugin_url = plugin_dir_url( __FILE__ );
	require_once $plugin_dir . 'includes/class-lbcg.php';
	LBCG::get_instance( '1.4.1', $plugin_dir, $plugin_url );
}

add_action( 'init', 'lbcg_register_custom_post_types', 0 );
function lbcg_register_custom_post_types() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-lbcg-helper.php';
	LBCG_Helper::register_custom_post_types();
}