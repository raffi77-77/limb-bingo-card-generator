<?php
/**
 * The Template for displaying bingo card invitation page
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$cu_email = '';
if (is_user_logged_in()) {
    global $current_user;
    $cu_email = $current_user->user_email;
}

if (isset($_GET['bc'])) {
    $bc_posts = get_posts([
        'name' => $_GET['bc'],
        'post_type' => 'bingo_card',
        'posts_per_page' => 1,
        'post_status' => 'publish'// 'draft'
    ]);
} else {
    $bc_posts = [];
}
if (!empty($bc_posts[0]->ID)) {
    $bingo_card = $bc_posts[0];
    $bc_permalink = get_permalink($bingo_card->ID);
    // Get bingo card data
    $data = get_post_meta($bingo_card->ID);
    // Type, size, title
    $bingo_card_type = $data['bingo_card_type'][0];
    $bingo_grid_size = $data['bingo_grid_size'][0];
    $bingo_card_title = $data['bingo_card_title'][0];
    // Special title
    if ($bingo_card_type === '1-75') {
        $bingo_card_spec_title = explode('|', $data['bingo_card_spec_title'][0]);
    }
    // Bingo card words
    if ($bingo_card_type === '1-75') {
        $bingo_card_words = BingoCardHelper::get_1_75_bingo_card_words();
    } else {
        // Get bingo card words
        if (!empty($data['bingo_card_content'][0])) {
            $bingo_card_content = $data['bingo_card_content'][0];
        } else {
            $result = BingoCardHelper::get_bg_default_content($bingo_card_type, $bingo_grid_size);
            $bingo_card_content = $result['words'];
        }
        $bingo_card_words = explode("\r\n", $bingo_card_content);
    }
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
            <aside class="lbcg-sidebar">
                <form action="<?php echo $bc_permalink . 'all/'; ?>" method="get" target="_blank">
                    <div class="lbcg-input-wrap">
                        <label for="lbcg-cards-count" class="lbcg-label">Cards per page count</label>
                        <select name="bcc" id="lbcg-cards-count" class="lbcg-select">
                            <option value="30" selected>30 cards</option>
                            <option value="100">100 cards</option>
                            <option value="250">250 cards</option>
                            <option value="500">500 cards</option>
                        </select>
                    </div>
                    <div class="lbcg-input-wrap">
                        <label for="lbcg-cards-per-page" class="lbcg-label">Cards per page count</label>
                        <select name="bcs" id="lbcg-cards-per-page" class="lbcg-select">
                            <option value="1">1 large card</option>
                            <option value="2" selected="selected">2 medium cards</option>
                            <option value="4">4 small cards</option>
                        </select>
                    </div>
                    <input type="submit" value="View">
                </form>
            </aside>

            <section class="lbcg-content">
                <div class="lbcg-content-left">
                    <form id="lbcg-bc-invitation" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
                        <input type="hidden" name="action" value="lbcg_bc_invitation">
                        <div class="lbcg-content-form">
                            <div class="lbcg-input-wrap">
                                <label for="cu-email">Your email:</label>
                                <input type="email" id="cu-email" name="author_email" value="<?php echo $cu_email; ?>"
                                       placeholder="Enter email">
                            </div>
                            <div class="lbcg-input-wrap">
                                <label for="invite-emails">Invite emails:</label>
                                <textarea id="invite-emails" name="invite_emails"
                                          placeholder="Enter invite emails, each in new line"></textarea>
                            </div>
                        </div>
                        <a role="button" href="<?php echo get_permalink(get_the_ID()) . '?bc=' . $_GET['bc']; ?>">Back</a>
                        <input type="submit" value="Invite">
                    </form>
                </div>
                <div class="lbcg-content-right">
                    <div class="lbcg-card-wrap">
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
                </div>
            </section>
        </main>
    </div>
    <?php
}
get_footer();
