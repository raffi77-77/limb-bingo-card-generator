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

$bc_posts = get_posts([
    'name' => $_GET['bc'],
    'post_type' => 'bingo_card',
    'posts_per_page' => 1,
    'post_status' => 'draft',
]);
if (!empty($bc_posts[0]->ID)) {
    $bingo_card = $bc_posts[0];
    ?>
    <div class="custom-container" style="width: 900px; margin: 0 auto;">
        <main class="lbcg-parent">
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
            </section>
        </main>
    </div>
    <?php
}
get_footer();
