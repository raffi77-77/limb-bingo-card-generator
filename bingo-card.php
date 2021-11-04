<?php
/**
 * Plugin Name: Bingo Card
 * Description: Generate and share Bingo Cards
 * Version: 1.0.0
 * Author: Raffi Yeghiazaryan
 * Author URI: https://www.upwork.com/freelancers/~01ff918a8b3fff85af
 * License: GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

function run_bingo_card() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-bingo-card.php';
    BingoCard::get_instance();
}

add_action('init', 'run_bingo_card');
