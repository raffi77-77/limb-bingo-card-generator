<?php
/**
 * The Template for displaying bingo card
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

global $post;
$data = get_post_meta($post->ID);
if (!empty($data['bingo_card_own_content'][0])) {
    // Type, size, title
    $bingo_card_type = $data['bingo_card_type'][0];
    $bingo_grid_size = $data['bingo_grid_size'][0];
    $bingo_card_title = $data['bingo_card_title'][0];
    // Special title
    if ($bingo_card_type === '1-75') {
        $bingo_card_spec_title = explode('|', $data['bingo_card_spec_title'][0]);
    }
    // Bingo card words
    $bingo_card_words = explode("\r\n", $data['bingo_card_own_content'][0]);
    // Header style
    $bc_header = unserialize($data['bc_header'][0]);
    // Grid style
    $bc_grid = unserialize($data['bc_grid'][0]);
    // Card style
    $bc_card = unserialize($data['bc_card'][0]);
    // If include free space
    $bingo_grid_free_square = $data['bingo_card_free_square'][0] === 'on';
    ?>
    <div class="custom-container" style="width: 900px; margin: 0 auto;">
        <main class="lbcg-parent">
            <div class="lbcg-card-wrap" style="min-width: 350px;">
                <?php include __DIR__ . '/props-template.php'; ?>
                <div class="lbcg-card">
                    <div class="lbcg-card-header-holder">
                        <div class="lbcg-card-header">
                            <span class="lbcg-card-header-text"><?php echo $bingo_card_title; ?></span>
                        </div>
                        <?php if ($bingo_card_type === '1-75'): ?>
                            <div class="lbcg-card-subtitle">
                                <span class="lbcg-card-subtitle-text"><span><?php echo implode('</span><span>', $bingo_card_spec_title); ?></span></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="lbcg-card-body">
                        <div class="lbcg-card-body-grid lbcg-grid-<?php echo $bingo_grid_size[0]; ?>">
                            <?php
                            $grid_sq_count = $bingo_grid_size[0] ** 2;
                            for ($i = 1; $i <= $grid_sq_count; $i++): ?>
                                <div class="lbcg-card-col">
                                    <span class="lbcg-card-text"><?php
                                        if ((int)ceil($grid_sq_count / 2) === $i && $bingo_grid_free_square) {
                                            echo BingoCardHelper::$free_space_word;
                                        } else {
                                            echo $bingo_card_words[$i - 1];
                                        }
                                        ?></span>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <?php
} else {
    ?>
    <p>Invalid card.</p>
    <?php
}
get_footer();
