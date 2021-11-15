<?php
/**
 * The Template for displaying bingo card generation page
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

global $post;
$current_id = $post->ID;
if (!empty($_GET['bc'])) {
    $bc_posts = get_posts([
        'name' => $_GET['bc'],
        'post_type' => 'bingo_card',
        'posts_per_page' => 1,
        'post_status' => 'draft',
    ]);
    if (!empty($bc_posts[0]->ID)) {
        $data = get_post_meta($bc_posts[0]->ID);
    }
}
if (empty($data)) {
    $data = get_post_meta($current_id);
}
$bingo_card_type = !empty($data['bingo_card_type'][0]) ? $data['bingo_card_type'][0] : '1-9';
$bingo_grid_size = !empty($data['bingo_grid_size'][0]) ? $data['bingo_grid_size'][0] : '3x3';
$bingo_card_title = !empty($data['bingo_card_title'][0]) ? $data['bingo_card_title'][0] : '';
// Special title
if (!empty($data['bingo_card_spec_title'][0])) {
    $bingo_card_spec_title = explode('|', $data['bingo_card_spec_title'][0]);
} else {
    $bingo_card_spec_title = ['B', 'I', 'N', 'G', 'O'];
}
// Get bingo card words
$result = BingoCardHelper::get_bg_default_content($bingo_card_type, $bingo_grid_size);
$words_count = $result['words_count'];
if (!empty($data['bingo_card_content'][0])) {
    $bingo_card_content = $data['bingo_card_content'][0];
} else {
    $bingo_card_content = $result['words'];
}
// Bingo card words
if ($bingo_card_type === '1-75') {
    $bingo_card_words = BingoCardHelper::get_1_75_bingo_card_words();
} else {
    $bingo_card_words = explode("\r\n", $bingo_card_content);
//    shuffle($bingo_card_words);
}
// Header style
if (!empty($data['bc_header'][0])) {
    $bc_header = unserialize($data['bc_header'][0]);
} else {
    $bc_header = [
        'color' => '#d6be89',
        'image' => '',
        'opacity' => 0,
        'repeat' => 'no-repeat'
    ];
}
// Grid style
if (!empty($data['bc_grid'][0])) {
    $bc_grid = unserialize($data['bc_grid'][0]);
} else {
    $bc_grid = [
        'color' => '#997d3c',
        'image' => '',
        'opacity' => 0,
        'repeat' => 'no-repeat'
    ];
}
// Card style
if (!empty($data['bc_card'][0])) {
    $bc_card = unserialize($data['bc_card'][0]);
} else {
    $bc_card = [
        'color' => '#d6be89',
        'image' => '',
        'opacity' => 0,
        'repeat' => 'no-repeat'
    ];
}
// If include free space
if (!empty($data['bingo_card_free_square'][0]) && $data['bingo_card_free_square'][0] === 'on' && $bingo_grid_size !== '4x4' || $bingo_card_type === '1-75') {
    $bingo_grid_free_square = true;
} else {
    $bingo_grid_free_square = false;
}
?>

    <div class="custom-container" style="width: 900px; margin: 0 auto;">
        <main class="lbcg-parent">
            <aside class="lbcg-sidebar">
                <div class="lbcg-sidebar-in collapsed">
                    <div class="lbcg-sidebar-header">
                        <a href="#" class="lbcg-sidebar-btn">Bingo Types</a>
                        <span class="lbcg-sidebar-arrow"></span>
                    </div>
                    <div class="lbcg-sidebar-body">
                        <?php
                        $args = array(
                            'post_type' => 'bingo_theme',
                            'post_status' => 'publish',
                            'orderby' => 'title',
                            'order' => 'ASC'
                        );
                        $query = new WP_Query($args);
                        if ($query->have_posts()) {
                            while ($query->have_posts()) {
                                $query->the_post();
                                ?>
                                <a href="<?php echo esc_url(get_permalink(get_the_ID())); ?>"
                                   class="lbcg-sidebar-link <?php echo $current_id === get_the_ID() ? 'active' : ''; ?>"><?php the_title(); ?></a>
                                <?php
                            }
                            wp_reset_postdata();
                        }
                        ?>
                    </div>
                </div>
            </aside>

            <section class="lbcg-content">
                <div class="lbcg-content-left">
                    <form id="lbcg-bc-generation" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="lbcg_bc_generation">
                        <input type="hidden" name="bingo_theme_id" value="<?php echo $current_id; ?>">
                        <input type="hidden" name="bingo_card_type" value="<?php echo $bingo_card_type; ?>">
                        <div class="lbcg-content-form">
                            <div class="lbcg-input-wrap">
                                <label for="lbcg-title" class="lbcg-label">Enter a Title</label>
                                <input class="lbcg-input" id="lbcg-title" type="text" name="bingo_card_title"
                                       value="<?php echo !empty($data['bingo_card_title'][0]) ? $data['bingo_card_title'][0] : ''; ?>"/>
                            </div>
                            <div class="lbcg-input-wrap" <?php echo !(!empty($data['bingo_card_type'][0]) && $data['bingo_card_type'][0] === '1-75') ? 'style="display: none;"' : ''; ?>>
                                <label for="lbcg-title" class="lbcg-label">Enter a Subtitle</label>
                                <div class="lbcg-input-wrap-in lbcg-input-wrap--subtitle">
                                    <label class="lbcg-label lbcg-label--single">
                                        <input class="lbcg-input" id="lbcg-subtitle-1" size="1" maxlength="1"
                                               name="bingo_card_spec_title[0]"
                                               type="text" value="<?php echo $bingo_card_spec_title[0]; ?>"/>
                                    </label>
                                    <label class="lbcg-label lbcg-label--single">
                                        <input class="lbcg-input" id="lbcg-subtitle-2" size="1" maxlength="1"
                                               name="bingo_card_spec_title[1]"
                                               type="text" value="<?php echo $bingo_card_spec_title[1]; ?>"/>
                                    </label>
                                    <label class="lbcg-label lbcg-label--single">
                                        <input class="lbcg-input" id="lbcg-subtitle-3" size="1" maxlength="1"
                                               name="bingo_card_spec_title[2]"
                                               type="text" value="<?php echo $bingo_card_spec_title[2]; ?>"/>
                                    </label>
                                    <label class="lbcg-label lbcg-label--single">
                                        <input class="lbcg-input" id="lbcg-subtitle-4" size="1" maxlength="1"
                                               name="bingo_card_spec_title[3]"
                                               type="text" value="<?php echo $bingo_card_spec_title[3]; ?>"/>
                                    </label>
                                    <label class="lbcg-label lbcg-label--single">
                                        <input class="lbcg-input" id="lbcg-subtitle-5" size="1" maxlength="1"
                                               name="bingo_card_spec_title[4]"
                                               type="text" value="<?php echo $bingo_card_spec_title[4]; ?>"/>
                                    </label>
                                </div>
                            </div>
                            <div class="lbcg-input-wrap" <?php echo !(!empty($data['bingo_card_type'][0]) && $data['bingo_card_type'][0] !== '1-75' && $data['bingo_card_type'][0] !== '1-90') ? 'style="display: none;' : ''; ?>>
                                <label for="lbcg-body-content" class="lbcg-label">Enter words/emojis or numbers</label>
                                <textarea class="lbcg-input" id="lbcg-body-content" name="bingo_card_content" cols=""
                                          rows="11"><?php echo !empty($data['bingo_card_content'][0]) ? $data['bingo_card_content'][0] : ''; ?></textarea>
                            </div>
                            <div class="lbcg-input-wrap" <?php echo !($bingo_card_type !== '1-9' && $bingo_card_type !== '1-75' && $bingo_card_type !== '1-90') ? 'style="display: none;"' : ''; ?>>
                                <label for="lbcg-grid-size" class="lbcg-label">Select Grid Size</label>
                                <select name="bingo_grid_size" id="lbcg-grid-size" class="lbcg-select">
                                    <option value="3x3" <?php echo $bingo_grid_size === '3x3' ? 'selected' : ''; ?>>
                                        3x3
                                    </option>
                                    <option value="4x4" <?php echo $bingo_grid_size === '4x4' ? 'selected' : ''; ?>>
                                        4x4
                                    </option>
                                    <option value="5x5" <?php echo $bingo_grid_size === '5x5' ? 'selected' : ''; ?>>
                                        5x5
                                    </option>
                                </select>
                            </div>
                            <div class="lbcg-input-wrap">
                                <label for="lbcg-font" class="lbcg-label">Select Font Family</label>
                                <select name="bingo_card_font" id="lbcg-font" class="lbcg-select">
                                    <?php foreach (BingoCardHelper::$fonts as $key => $font): ?>
                                        <option value="<?php echo $key; ?>" <?php echo !empty($data['bingo_card_font'][0]) && $data['bingo_card_font'][0] === $key ? 'selected="selected"' : ''; ?>><?php
                                            echo $font['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="lbcg-input-wrap" <?php echo ($bingo_card_type === '1-75' || $bingo_card_type === '1-90' || $bingo_grid_size === '4x4') ? 'style="display: none;"' : ''; ?>>
                                <input type="checkbox" class="lbcg-checkbox" id="lbcg-free-space-check"
                                       name="bingo_card_free_square"
                                       hidden <?php echo $bingo_grid_free_square ? 'checked' : ''; ?>/>
                                <label for="lbcg-free-space-check" class="lbcg-checkbox-holder"></label>
                                <label for="lbcg-free-space-check" class="lbcg-label">Include free space?</label>
                            </div>
                            <div class="lbcg-input-wrap">
                                <input type="checkbox" class="lbcg-checkbox lbcg-checkbox--collapse" id="lbcg-header-bg-check" hidden/>
                                <label for="lbcg-header-bg-check" class="lbcg-checkbox-holder"></label>
                                <label for="lbcg-header-bg-check" class="lbcg-label">Header Background</label>
                                <div class="lbcg-input-wrap-in lbcg-input-wrap--collapse">
                                    <div class="lbcg-input-wrap">
                                        <label for="bc-header-color" class="lbcg-label">Background Color</label>
                                        <input type="color" id="bc-header-color" class="lbcg-input" name="bc_header[color]" value="<?php echo $bc_header['color']; ?>" data-bct="header">
                                    </div>
                                    <div class="lbcg-input-wrap">
                                        <label for="bc-header-image" class="lbcg-label">Image</label>
                                        <input hidden type="file" accept="image/*" id="bc-header-image" class="bc-image lbcg-input" name="bc_header[image]" value="<?php echo !empty($bc_header['image']) ? $bc_header['image'] : '0'; ?>" data-bct="header">
                                        <label for="bc-header-image" class="lbcg-btn lbcg-btn--main">Choose File</label>
                                        <button class="lbcg-btn lbcg-btn--remove remove-bc-image" data-bct="header"></button>
                                        <input type="hidden" name="bc_header[remove_image]" value="0">
                                    </div>
                                    <div class="lbcg-input-wrap">
                                        <label for="bc-header-repeat" class="lbcg-label">Repeat</label>
                                        <input hidden type="checkbox" class="lbcg-checkbox" id="bc-header-repeat" class="bc-repeat" name="bc_header[repeat]" <?php echo $bc_grid['repeat'] === 'repeat' ? 'checked' : ''; ?> data-bct="header" value="repeat" hidden/>
                                        <label for="bc-header-repeat" class="lbcg-checkbox-holder"></label>
                                    </div>
                                    <div class="lbcg-input-wrap">
                                        <label for="bc-header-opacity" class="lbcg-label">Opacity (%)</label>
                                        <input type="number" id="bc-header-opacity" class="bc-opacity lbcg-input lbcg-input--opacity" name="bc_header[opacity]" min="0" max="100" placeholder="0-100" value="<?php echo $bc_header['opacity']; ?>" data-bct="header">
                                    </div>
                                    <div class="lbcg-input-wrap">
                                        <label class="lbcg-label">
                                            <select name="lbcg-bg-pos" id="lbcg-bg-pos" class="lbcg-select">
                                                <option value="0" disabled selected>Background Position</option>
                                                <option value="top left">Top Left</option>
                                                <option value="top center">Top Center</option>
                                                <option value="top right">Top Right</option>
                                                <option value="center left">Center Left</option>
                                                <option value="center center">Center Center</option>
                                                <option value="center right">Center Right</option>
                                                <option value="bottom left">Bottom Left</option>
                                                <option value="bottom center">Bottom Center</option>
                                                <option value="bottom right">Bottom Right</option>
                                            </select>
                                        </label>
                                    </div>
                                    <div class="lbcg-input-wrap">
                                        <label class="lbcg-label">
                                            <select name="lbcg-bg-rep" id="lbcg-bg-rep" class="lbcg-select">
                                                <option value="no-repeat" disabled selected>Background Repeat</option>
                                                <option value="repeat">Repeat</option>
                                                <option value="repeat-x">Repeat X</option>
                                                <option value="repeat-y">Repeat Y</option>
                                                <option value="no-repeat">No Repeat</option>
                                            </select>
                                        </label>
                                    </div>
                                    <div class="lbcg-input-wrap">
                                        <label class="lbcg-label">
                                            <select name="lbcg-bg-size" id="lbcg-bg-size" class="lbcg-select">
                                                <option value="auto" disabled selected>Background Size</option>
                                                <option value="auto">Auto</option>
                                                <option value="contain">Contain</option>
                                                <option value="cover">Cover</option>
                                            </select>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="lbcg-input-wrap">
                                <input type="checkbox" class="lbcg-checkbox lbcg-checkbox--collapse" id="lbcg-grid-bg-check" hidden/>
                                <label for="lbcg-grid-bg-check" class="lbcg-checkbox-holder"></label>
                                <label for="lbcg-grid-bg-check" class="lbcg-label">Grid Background</label>
                                <div class="lbcg-input-wrap-in lbcg-input-wrap--collapse">
                                    <div class="lbcg-input-wrap">
                                        <label for="bc-grid-color" class="lbcg-label">Background Color</label>
                                        <input type="color" id="bc-grid-color" class="bc-color lbcg-input" name="bc_grid[color]" value="<?php echo $bc_grid['color']; ?>" data-bct="grid">
                                    </div>
                                    <div class="lbcg-input-wrap">
                                        <label for="bc-grid-image" class="lbcg-label">Image</label>
                                        <input hidden type="file" accept="image/*" id="bc-grid-image" class="bc-image lbcg-input" name="bc_grid[image]" value="<?php echo !empty($bc_grid['image']) ? $bc_grid['image'] : '0'; ?>" data-bct="grid">
                                        <label for="bc-grid-image" class="lbcg-btn lbcg-btn--main">Choose File</label>
                                        <button class="remove-bc-image lbcg-btn lbcg-btn--remove" data-bct="grid"></button>
                                        <input type="hidden" name="bc_grid[remove_image]" value="0">
                                    </div>
                                    <div class="lbcg-input-wrap">
                                        <label for="bc-grid-repeat" class="lbcg-label">Repeat</label>
                                        <input hidden type="checkbox" id="bc-grid-repeat" class="bc-repeat" name="bc_grid[repeat]" <?php echo $bc_grid['repeat'] === 'repeat' ? 'checked' : ''; ?> data-bct="grid" value="repeat">
                                        <label for="bc-grid-repeat" class="lbcg-checkbox-holder"></label>
                                    </div>
                                    <div class="lbcg-input-wrap">
                                        <label for="bc-grid-opacity" class="lbcg-label">Opacity (%)</label>
                                        <input type="number" id="bc-grid-opacity" class="bc-opacity lbcg-input lbcg-input--opacity" name="bc_grid[opacity]" min="0" max="100" placeholder="0-100" value="<?php echo $bc_grid['opacity']; ?>" data-bct="grid">
                                    </div>
                                </div>
                            </div>
                            <div class="lbcg-input-wrap">
                                <input type="checkbox" class="lbcg-checkbox lbcg-checkbox--collapse" id="lbcg-card-bg-check" hidden/>
                                <label for="lbcg-card-bg-check" class="lbcg-checkbox-holder"></label>
                                <label for="lbcg-card-bg-check" class="lbcg-label">Card Background</label>
                                <div class="lbcg-input-wrap-in lbcg-input-wrap--collapse">
                                    <div class="lbcg-input-wrap">
                                        <label for="bc-card-color" class="lbcg-label">Background Color</label>
                                        <input type="color" id="bc-card-color" class="bc-color lbcg-input" name="bc_card[color]" value="<?php echo $bc_card['color']; ?>" data-bct="card">
                                    </div>
                                    <div class="lbcg-input-wrap">
                                        <label for="bc-card-image" class="lbcg-label">Image</label>
                                        <input hidden type="file" accept="image/*" id="bc-card-image" class="bc-image lbcg-input" name="bc_card[image]" value="<?php echo !empty($bc_card['image']) ? $bc_card['image'] : '0'; ?>" data-bct="card">
                                        <label for="bc-card-image" class="lbcg-btn lbcg-btn--main">Choose File</label>
                                        <button class="remove-bc-image lbcg-btn lbcg-btn--remove" data-bct="card"></button>
                                        <input type="hidden" name="bc_card[remove_image]" value="0">
                                    </div>
                                    <div class="lbcg-input-wrap">
                                        <label for="bc-card-repeat" class="lbcg-label">Repeat</label>
                                        <input hidden type="checkbox" id="bc-card-repeat" class="bc-repeat" name="bc_card[repeat]" <?php echo $bc_card['repeat'] === 'repeat' ? 'checked' : ''; ?> data-bct="card" value="repeat">
                                        <label for="bc-card-repeat" class="lbcg-checkbox-holder"></label>
                                    </div>
                                    <div class="lbcg-input-wrap">
                                        <label for="bc-card-opacity" class="lbcg-label">Opacity (%)</label>
                                        <input type="number" id="bc-card-opacity" class="bc-opacity lbcg-input lbcg-input--opacity" name="bc_card[opacity]" min="0" max="100" placeholder="0-100" value="<?php echo $bc_card['opacity']; ?>" data-bct="card">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="lbcg-input-wrap">
                            <button class="lbcg-btn lbcg-btn--lg lbcg-btn--main" type="submit">Generate Bingo Card</button>
                        </div>
                    </form>
                </div>
                <div class="lbcg-content-right">
                    <div class="lbcg-card-wrap">
                        <style type="text/css">
                            :root {
                                /* card styles */

                                --lbcg-card-bg-image: <?php echo !empty($bc_card['image']) ? 'url(' . wp_get_attachment_image_url($bc_card['image'], 'large') . ')' : 'none'; ?>;
                                --lbcg-card-bg-size: '<?php echo !empty($bc_card['size']) ? $bc_card['size'] : 'auto'; ?>';
                                --lbcg-card-bg-pos: '<?php echo !empty($bc_card['pos']) ? $bc_card['pos'] : '0 0'; ?>';
                                --lbcg-card-bg-repeat: '<?php echo !empty($bc_card['repeat'])  ? $bc_card['repeat'] : 'no-repeat'; ?>';
                                --lbcg-card-bg-color: <?php echo !empty($bc_card['color']) ? $bc_card['color'] : '#FFF'; ?>;
                                --lbcg-card-bg-opacity: <?php echo isset($bc_card['opacity']) ? $bc_card['opacity'] / 100 : 1; ?>;

                                /* header styles */

                                --lbcg-header-font-size: 16px;
                                --lbcg-header-height: 48px;
                                --lbcg-header-font-family: '<?php echo !empty($data['bingo_card_font'][0]) ? BingoCardHelper::$fonts[$data['bingo_card_font'][0]]['name'] : 'Roboto'; ?>', sans-serif;
                                --lbcg-header-text-color: #FFF;
                                --lbcg-header-bg-image: <?php echo !empty($bc_header['image']) ? 'url(' . wp_get_attachment_image_url($bc_header['image'], 'large') . ')' : 'none' ?>;
                                --lbcg-header-bg-size: '<?php echo !empty($bc_header['size']) ? $bc_header['size'] : 'auto'; ?>';
                                --lbcg-header-bg-pos: '<?php echo !empty($bc_header['pos']) ? $bc_header['pos'] : '0 0'; ?>';
                                --lbcg-header-bg-repeat: '<?php echo !empty($bc_header['repeat']) ? $bc_header['repeat'] : 'no-repeat'; ?>';
                                --lbcg-header-bg-color: <?php echo !empty($bc_header['color']) ? $bc_header['color'] : 'transparent'; ?>;
                                --lbcg-header-bg-opacity: <?php echo isset($bc_header['opacity']) ? $bc_header['opacity'] / 100 : 1; ?>;

                                /* body styles */

                                --lbcg-grid-font-size: 16px; /* esi Vah jan gtnumes es <span class="lbcg-card-text"> srancic amenamec heightov@ U HAMEL amena erkar u dnumes es variable-i mej */
                                --lbcg-grid-font-family: '<?php echo !empty($data['bingo_card_font'][0]) ? BingoCardHelper::$fonts[$data['bingo_card_font'][0]]['name'] : 'Roboto'; ?>', sans-serif;
                                --lbcg-grid-line-height: 61.8px; /* esi Vah jan vercnum es <div class="lbcg-card-col"> sra height@ u dnumes es variable-i mej */
                                --lbcg-grid-text-color: #79ffd3;
                                --lbcg-grid-border-color: #45ffbf;
                                --lbcg-grid-bg-image: <?php echo !empty($bc_grid['image']) ? 'url(' . wp_get_attachment_image_url($bc_grid['image'], 'large') . ')' : 'none'; ?>;
                                --lbcg-grid-bg-size: '<?php echo !empty($bc_grid['size']) ? $bc_grid['size'] : 'auto'; ?>';
                                --lbcg-grid-bg-pos: '<?php echo !empty($bc_grid['pos']) ? $bc_grid['pos'] : '0 0'; ?>';
                                --lbcg-grid-bg-repeat: '<?php echo !empty($bc_grid['repeat']) ? $bc_grid['repeat'] : 'no-repeat'; ?>';
                                --lbcg-grid-bg-color: <?php echo !empty($bc_grid['color']) ? $bc_grid['color'] : '#003221'; ?>;
                                --lbcg-grid-bg-opacity: <?php echo isset($bc_grid['opacity']) ? $bc_grid['opacity'] / 100 : .5; ?>;
                            }
                        </style>
                        <div class="lbcg-card">
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
get_footer();
