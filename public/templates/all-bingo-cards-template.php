<?php
/**
 * The Template for displaying all generated bingo cards
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

global $post;
$all = BingoCardHelper::generate_all_content_info($post->ID, $_GET['bcc']);

?>
<div class="custom-container" style="width: 900px; margin: 0 auto;">
    <main class="lbcg-parent">
        <section class="lbcg-content">
            <!--<?php foreach ($all as $content): ?>
            <p><?php echo $content; ?></p>
            <?php endforeach; ?>-->
            Generated bingo cards
        </section>
    </main>
</div>
<?php
get_footer();
