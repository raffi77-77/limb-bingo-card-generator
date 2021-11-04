<?php
/**
 * The Template for displaying bingo card generation page
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header(); ?>

<?php
//while (have_posts()) :
//    the_post();
//endwhile;

global $post;
$bingo_card_type = get_post_meta($post->ID, 'bingo_card_type', true);
$bingo_grid_size = get_post_meta($post->ID, 'bingo_grid_size', true);
?>

<div class="container">
    Bingo card type: <?php echo $bingo_card_type; ?>
    Bingo grid size: <?php echo $bingo_grid_size; ?>
</div>

<?php
get_footer();
