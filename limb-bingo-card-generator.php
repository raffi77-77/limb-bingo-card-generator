<?php
/**
 * Plugin Name: Limb Bingo Cards Generator
 * Description: Generate, share or print bingo cards
 * Version: 1.1.1
 * Author: Raffi Yeghiazaryan
 * Author URI: https://www.upwork.com/freelancers/~01ff918a8b3fff85af
 * License: GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
add_action( 'init', 'run_lbcg' );
function run_lbcg() {
	$plugin_dir = plugin_dir_path( __FILE__ );
	$plugin_url = plugin_dir_url( __FILE__ );
	require_once $plugin_dir . 'includes/class-lbcg.php';
	LBCG::get_instance( '1.1.1', $plugin_dir, $plugin_url );
}

add_action( 'init', 'lbcg_register_custom_post_types', 0 );
function lbcg_register_custom_post_types() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-lbcg-helper.php';
	LBCG_Helper::register_custom_post_types();
}