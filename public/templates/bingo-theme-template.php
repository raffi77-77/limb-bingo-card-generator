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
$data = get_post_meta($post->ID);
?>

<div class="custom-container" style="width: 900px; margin: 0 auto">
    <main class="lbcg-parent">
        <aside class="lbcg-sidebar">
            <div class="lbcg-sidebar-in collapsed"><!-- collapsed for dropdown -->
                <div class="lbcg-sidebar-header">
                    <a href="#" class="lbcg-sidebar-btn">Popular</a>
                    <span class="lbcg-sidebar-arrow"></span>
                </div>
                <div class="lbcg-sidebar-body">
                    <a href="#" class="lbcg-sidebar-link">Bingo Card Generator</a>
                    <a href="#" class="lbcg-sidebar-link">1-75 Bingo</a>
                    <a href="#" class="lbcg-sidebar-link">1-90 Bingo</a>
                    <a href="#" class="lbcg-sidebar-link">Virtual Bingo</a>
                    <a href="#" class="lbcg-sidebar-link">Online Escape Rooms</a>
                    <a href="#" class="lbcg-sidebar-link">Find My Order</a>
                </div>
            </div>
            <div class="lbcg-sidebar-in"><!-- collapsed for dropdown -->
                <div class="lbcg-sidebar-header">
                    <a href="#" class="lbcg-sidebar-btn">Numbers</a>
                    <span class="lbcg-sidebar-arrow"></span>
                </div>
                <div class="lbcg-sidebar-body">
                    <a href="#" class="lbcg-sidebar-link">1-75</a>
                    <a href="#" class="lbcg-sidebar-link">1-90</a>
                    <a href="#" class="lbcg-sidebar-link">1-100</a>
                    <a href="#" class="lbcg-sidebar-link">1-25</a>
                    <a href="#" class="lbcg-sidebar-link">1-25 Words</a>
                    <a href="#" class="lbcg-sidebar-link">1-20</a>
                    <a href="#" class="lbcg-sidebar-link">1-9</a>
                </div>
            </div>
        </aside>

        <section class="lbcg-content">
            <div class="lbcg-content-left">
                <div class="lbcg-content-form">
                    <div class="lbcg-input-wrap">
                        <label for="lbcg-title" class="lbcg-label">Enter a Title</label>
                        <input class="lbcg-input" id="lbcg-title" type="text" value="<?php echo !empty($data['bingo_card_title'][0]) ? $data['bingo_card_title'][0] : ''; ?>" />
                    </div>
                    <div class="lbcg-input-wrap">
                        <label for="lbcg-body-content" class="lbcg-label">Enter word or numbers</label>
                        <textarea class="lbcg-input" id="lbcg-body-content" name="lbcg-body-content" cols="" rows="11"><?php echo !empty($data['bingo_card_content'][0]) ? $data['bingo_card_content'][0] : ''; ?></textarea>
                    </div>
                </div>
            </div>
            <div class="lbcg-content-right">
                <div class="lbcg-card-wrap">
                    <div class="lbcg-card">
                        <style type="text/css">
                            :root {
                                /* card styles */

                                --lbcg-card-bg-color: #FFF;
                                --lbcg-card-bg-image: url(https://thumbs.dreamstime.com/z/crazy-cat-tongue-hanging-out-40087599.jpg);

                                /* header styles */

                                --lbcg-card-header-font-size: 16px;
                                --lbcg-card-header-height: 48px;
                                --lbcg-card-header-font-family: 'Roboto', sans-serif;
                                --lbcg-card-header-text-color: #FFF;
                                --lbcg-card-header-bg-color: transparent;
                                --lbcg-card-header-bg-image: none;

                                /* body styles */

                                --lbcg-card-col-font-size: 16px;/* esi Vah jan gtnumes es <span class="lbcg-card-text"> srancic amenamec heightov@ U HAMEL amena erkar u dnumes es variable-i mej */
                                --lbcg-card-col-line-height: 61.8px;/* esi Vah jan vercnum es <div class="lbcg-card-col"> sra height@ u dnumes es variable-i mej */
                                /* js example */
                                /* let root = document.documentElement; */
                                /* root.style.setProperty('--lbcg-card-col-line-height', '60px'); */
                                --lbcg-card-col-text-color: #79ffd3;
                                --lbcg-card-col-bg-color: #003221;
                                --lbcg-card-col-border-color: #45ffbf;
                            }
                        </style>
                        <div class="lbcg-card-header">
                            <span class="lgbc-card-header-text">Make Your Own Bingo!</span>
                        </div>
                        <div class="lbcg-card-body">
                            <div class="lbcg-card-body-grid lbcg-grid-5"><!-- lbcg-grid-3/lbcg-grid-4 -->
                                <div class="lbcg-card-col"><span class="lbcg-card-text">1</span></div>
                                <div class="lbcg-card-col"><span class="lbcg-card-text">2</span></div>
                                <div class="lbcg-card-col"><span class="lbcg-card-text">3</span></div>
                                <div class="lbcg-card-col"><span class="lbcg-card-text">4</span></div>
                                <div class="lbcg-card-col"><span class="lbcg-card-text">5</span></div>
                                <div class="lbcg-card-col"><span class="lbcg-card-text">6</span></div>
                                <div class="lbcg-card-col"><span class="lbcg-card-text">7</span></div>
                                <div class="lbcg-card-col"><span class="lbcg-card-text">8</span></div>
                                <div class="lbcg-card-col"><span class="lbcg-card-text">9</span></div>
                                <div class="lbcg-card-col"><span class="lbcg-card-text">10</span></div>
                                <div class="lbcg-card-col"><span class="lbcg-card-text">11</span></div>
                                <div class="lbcg-card-col"><span class="lbcg-card-text">12</span></div>
                                <div class="lbcg-card-col"><span class="lbcg-card-text">13</span></div>
                                <div class="lbcg-card-col"><span class="lbcg-card-text">14</span></div>
                                <div class="lbcg-card-col"><span class="lbcg-card-text">15</span></div>
                                <div class="lbcg-card-col"><span class="lbcg-card-text">16</span></div>
                                <div class="lbcg-card-col"><span class="lbcg-card-text">17</span></div>
                                <div class="lbcg-card-col"><span class="lbcg-card-text">18</span></div>
                                <div class="lbcg-card-col"><span class="lbcg-card-text">19</span></div>
                                <div class="lbcg-card-col"><span class="lbcg-card-text">20</span></div>
                                <div class="lbcg-card-col"><span class="lbcg-card-text">21</span></div>
                                <div class="lbcg-card-col"><span class="lbcg-card-text">22</span></div>
                                <div class="lbcg-card-col"><span class="lbcg-card-text">23</span></div>
                                <div class="lbcg-card-col"><span class="lbcg-card-text">24</span></div>
                                <div class="lbcg-card-col"><span class="lbcg-card-text">25</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>

<?php
get_footer();
