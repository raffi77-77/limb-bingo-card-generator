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
?>
<div class="custom-container" style="width: 900px; margin: 0 auto;">
    <main class="lbcg-parent">
        <section class="lbcg-content">
            <div class="lbcg-content-left">
                <form id="bingo-card-invitation">
                    <input type="hidden" name="action" value="invitation">
                    <div class="lbcg-content-form">
                        <div class="lbcg-input-wrap">
                            <label for="cu-email">Your email:</label>
                            <input type="email" id="cu-email" name="author_email" value="<?php echo $cu_email; ?>" placeholder="Enter email">
                        </div>
                        <div class="lbcg-input-wrap">
                            <label for="invite-emails">Invite emails:</label>
                            <textarea id="invite-emails" name="invite_emails" placeholder="Enter invite emails, each in new line"></textarea>
                        </div>
                    </div>
                </form>
                <button id="invite-bc">Invite</button>
            </div>
        </section>
    </main>
</div>
<?php
get_footer();
