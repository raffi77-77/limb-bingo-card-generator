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
$data = get_post_meta($post->ID);
$bingo_card_type = !empty($data['bingo_card_type'][0]) ? $data['bingo_card_type'][0] : '1-9';
$bingo_grid_size = !empty($data['bingo_grid_size'][0]) ? $data['bingo_grid_size'][0] : '3x3';
$bingo_card_title = !empty($data['bingo_card_title'][0]) ? $data['bingo_card_title'][0] : '';
if (!empty($data['bingo_card_spec_title'][0])) {
    $bingo_card_spec_title = explode('|', $data['bingo_card_spec_title'][0]);
} else {
    $bingo_card_spec_title = ['B', 'I', 'N', 'G', 'O'];
}
$result = BingoCardHelper::get_bg_default_content($bingo_card_type, $bingo_grid_size);
$words_count = $result['words_count'];
if (!empty($data['bingo_card_content'][0])) {
    $bingo_card_content = $data['bingo_card_content'][0];
} else {
    $bingo_card_content = $result['words'];
}
if (!empty($data['bc_header'][0])) {
    $bc_header = unserialize($data['bc_header'][0]);
} else {
    $bc_header = [
        'color' => '#d6be89',
        'image' => '',
        'opacity' => 0,
        'repeat' => 'off'
    ];
}
if (!empty($data['bc_grid'][0])) {
    $bc_grid = unserialize($data['bc_grid'][0]);
} else {
    $bc_grid = [
        'color' => '#997d3c',
        'image' => '',
        'opacity' => 0,
        'repeat' => 'off'
    ];
}
if (!empty($data['bc_card'][0])) {
    $bc_card = unserialize($data['bc_card'][0]);
} else {
    $bc_card = [
        'color' => '#d6be89',
        'image' => '',
        'opacity' => 0,
        'repeat' => 'off'
    ];
}
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
                    <a href="#" class="lbcg-sidebar-link active"><!-- active for active -->Bingo Card Generator</a>
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
                    <?php if (!empty($data['bingo_card_type'][0]) && $data['bingo_card_type'][0] === '1-75'): ?>
                    <div class="lbcg-input-wrap">
                        <label for="lbcg-title" class="lbcg-label">Enter a Subtitle</label>
                        <div class="lbcg-input-wrap-in lbcg-input-wrap--subtitle">
                            <label class="lbcg-label lbcg-label--single">
                                <input class="lbcg-input" id="lbcg-subtitle-1" size="1" maxlength="1" type="text" value="<?php echo $bingo_card_spec_title[0]; ?>" />
                            </label>
                            <label class="lbcg-label lbcg-label--single">
                                <input class="lbcg-input" id="lbcg-subtitle-2" size="1" maxlength="1" type="text" value="<?php echo $bingo_card_spec_title[1]; ?>" />
                            </label>
                            <label class="lbcg-label lbcg-label--single">
                                <input class="lbcg-input" id="lbcg-subtitle-3" size="1" maxlength="1" type="text" value="<?php echo $bingo_card_spec_title[2]; ?>" />
                            </label>
                            <label class="lbcg-label lbcg-label--single">
                                <input class="lbcg-input" id="lbcg-subtitle-4" size="1" maxlength="1" type="text" value="<?php echo $bingo_card_spec_title[3]; ?>" />
                            </label>
                            <label class="lbcg-label lbcg-label--single">
                                <input class="lbcg-input" id="lbcg-subtitle-5" size="1" maxlength="1" type="text" value="<?php echo $bingo_card_spec_title[4]; ?>" />
                            </label>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($data['bingo_card_type'][0]) && $data['bingo_card_type'][0] !== '1-75' && $data['bingo_card_type'][0] !== '1-90'): ?>
                    <div class="lbcg-input-wrap">
                        <label for="lbcg-body-content" class="lbcg-label">Enter words/emojis or numbers</label>
                        <textarea class="lbcg-input" id="lbcg-body-content" name="lbcg-body-content" cols="" rows="11"><?php echo !empty($data['bingo_card_content'][0]) ? $data['bingo_card_content'][0] : ''; ?></textarea>
                    </div>
                    <?php endif; ?>
                    <?php if ($bingo_card_type !== '1-9' && $bingo_card_type !== '1-75' && $bingo_card_type !== '1-90'): ?>
                    <div class="lbcg-input-wrap">
                        <label for="lbcg-grid-size" class="lbcg-label">Select Grid Size</label>
                        <select name="lbcg-grid-size" id="lbcg-grid-size" class="lbcg-select">
                            <option value="grid-3x3" <?php echo $bingo_grid_size === '3x3' ? 'selected' : ''; ?>>3x3</option>
                            <option value="grid-4x4" <?php echo $bingo_grid_size === '4x4' ? 'selected' : ''; ?>>4x4</option>
                            <option value="grid-5x5" <?php echo $bingo_grid_size === '5x5' ? 'selected' : ''; ?>>5x5</option>
                        </select>
                    </div>
                    <?php endif; ?>
                    <div class="lbcg-input-wrap">
                        <label for="lbcg-font" class="lbcg-label">Select Font Family</label>
                        <select name="lbcg-font" id="lbcg-font" class="lbcg-select">
                            <option value="roboto" selected>Roboto</option>
                            <option value="noto-sans">Noto Sans</option>
                        </select>
                    </div>
                    <?php if ($bingo_card_type !== '1-75' && $bingo_card_type !== '1-90' && $bingo_grid_size !== '4x4'): ?>
                    <div class="lbcg-input-wrap">
                        <input type="checkbox" class="lbcg-checkbox" id="lbcg-free-space-check" hidden <?php echo !empty($data['bingo_card_free_square'][0]) && $data['bingo_card_free_square'][0] === 'on' ? 'checked' : ''; ?>/>
                        <label for="lbcg-free-space-check" class="lbcg-checkbox-holder"></label>
                        <label for="lbcg-free-space-check" class="lbcg-label">Include free space?</label>
                    </div>
                    <?php endif; ?>
                    <div class="lbcg-input-wrap">
                        <input type="checkbox" class="lbcg-checkbox lbcg-checkbox--collapse" id="lbcg-bg-image-check" hidden />
                        <label for="lbcg-bg-image-check" class="lbcg-checkbox-holder"></label>
                        <label for="lbcg-bg-image-check" class="lbcg-label">Background Image</label>
                        <div class="lbcg-input-wrap-in lbcg-input-wrap--collapse">
                            <label class="lbcg-label">
                                <input type="file" class="lbcg-input" id="lbcg-bg-image" />
                            </label>
                            <label class="lbcg-label">
                                <select name="lbcg-bg-pos" id="lbcg-bg-pos" class="lbcg-select">
                                    <option value="0" disabled selected>Background Position</option>
                                    <option value="top-left">Top Left</option>
                                    <option value="top-center">Top Center</option>
                                </select>
                            </label>
                            <label class="lbcg-label">
                                <select name="lbcg-bg-rep" id="lbcg-bg-rep" class="lbcg-select">
                                    <option value="0" disabled selected>Background Repeat</option>
                                    <option value="repeat">Repeat</option>
                                    <option value="repeat-x">Repeat X</option>
                                    <option value="repeat-y">Repeat Y</option>
                                    <option value="no-repeat-y">No Repeat</option>
                                </select>
                            </label>
                            <label class="lbcg-label">
                                <select name="lbcg-bg-size" id="lbcg-bg-size" class="lbcg-select">
                                    <option value="0" disabled selected>Background Size</option>
                                    <option value="cover">Cover</option>
                                    <option value="contain">Contain</option>
                                </select>
                            </label>
                        </div>
                    </div>
                    <div class="lbcg-input-wrap">
                        <label for="lbcg-bg-color" class="lbcg-label">Background Color</label>
                        COLOR PICKER HERE
                    </div>
                    <div class="lbcg-input-wrap">
                        <label for="lbcg-bg-opacity" class="lbcg-label">Background Opacity</label>
                        <input type="number" min="0" max="100" placeholder="0-100" class="lbcg-input" id="lbcg-bg-opacity" />
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
                                --lbcg-card-bg-image: url(<?php echo !empty($bc_card['image']) ? wp_get_attachment_image_url($bc_card['image'], 'large') : 'https://thumbs.dreamstime.com/z/crazy-cat-tongue-hanging-out-40087599.jpg'; ?>);
                                --lbcg-card-bg-opacity: 1;

                                /* header styles */

                                --lbcg-card-header-font-size: 16px;
                                --lbcg-card-header-height: 48px;
                                --lbcg-card-header-font-family: 'Roboto', sans-serif;
                                --lbcg-card-header-text-color: #FFF;
                                --lbcg-card-header-bg-color: <?php echo !empty($bc_header['color']) ? $bc_header['color'] : 'transparent'; ?>;
                                --lbcg-card-header-bg-image: <?php echo !empty($bc_header['image']) ? 'url(' . wp_get_attachment_image_url($bc_header['image'], 'large') . ')' : 'none' ?>;
                                --lbcg-card-header-bg-opacity: 1;

                                /* body styles */

                                --lbcg-card-col-font-size: 16px;/* esi Vah jan gtnumes es <span class="lbcg-card-text"> srancic amenamec heightov@ U HAMEL amena erkar u dnumes es variable-i mej */
                                --lbcg-card-col-line-height: 61.8px;/* esi Vah jan vercnum es <div class="lbcg-card-col"> sra height@ u dnumes es variable-i mej */
                                /* js example */
                                /* let root = document.documentElement; */
                                /* root.style.setProperty('--lbcg-card-col-line-height', '60px'); */
                                --lbcg-card-col-text-color: #79ffd3;
                                --lbcg-card-col-bg-color: <?php echo !empty($bc_grid['color']) ? $bc_grid['color'] : '#003221'; ?>;
                                --lbcg-card-col-border-color: #45ffbf;
                                --lbcg-card-col-bg-opacity: .5;
                            }
                        </style>
                        <div class="lbcg-card-header-holder">
                            <div class="lbcg-card-header">
                                <span class="lbcg-card-header-text"><?php echo !empty($data['bingo_card_title'][0]) ? $data['bingo_card_title'][0] : ''; ?></span>
                            </div>
                            <?php if ($bingo_card_type === '1-75'): ?>
                            <div class="lbcg-card-subtitle">
                                <span class="lbcg-card-subtitle-text"><span><?php echo !empty($bingo_card_spec_title) ? implode('</span><span>', $bingo_card_spec_title) : ''; ?></span></span>
                            </div>
                            <?php endif; ?>
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
                                <div class="lbcg-card-col"><span class="lbcg-card-text">&#9733</span></div>
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
