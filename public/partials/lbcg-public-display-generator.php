<?php
/**
 * The Template for displaying bingo card generation page
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$lbcg_current_theme_name = wp_get_theme()->get( 'Name' );
if ( $lbcg_current_theme_name === 'BNBS' ) {
	// For BNBS theme header
	$tpl             = 'single-bingo-card';
	$lyt             = 'single-bingo-card';
	$ftd_pagetyp     = 'custom-post';
	$body_page_class = $tpl;
	require( get_template_directory() . '/header.php' );
} else {
	get_header();
}
global $post;
$current_id = $post->ID;
if ( ! empty( $_GET['bc'] ) ) {
	$bc_posts = get_posts( [
		'name'           => $_GET['bc'],
		'post_type'      => 'bingo_card',
		'posts_per_page' => 1,
		'post_status'    => 'publish',
	] );
	if ( ! empty( $bc_posts[0]->ID ) ) {
		$data = get_post_meta( $bc_posts[0]->ID );
	}
}
if ( empty( $data ) ) {
	$data = get_post_meta( $current_id );
}
$bingo_card_type  = ! empty( $data['bingo_card_type'][0] ) ? $data['bingo_card_type'][0] : 'generic';
$bingo_grid_size  = ! empty( $data['bingo_grid_size'][0] ) ? $data['bingo_grid_size'][0] : '3x3';
$bingo_card_title = ! empty( $data['bingo_card_title'][0] ) ? $data['bingo_card_title'][0] : '';
// Special title
if ( ! empty( $data['bingo_card_spec_title'][0] ) ) {
	$bingo_card_spec_title = explode( '|', $data['bingo_card_spec_title'][0] );
} else {
	$bingo_card_spec_title = [ 'B', 'I', 'N', 'G', 'O' ];
}
// Bingo card words
if ( $bingo_card_type === '1-75' ) {
	$bingo_card_words = LBCG_Helper::get_1_75_bingo_card_numbers();
} elseif ( $bingo_card_type === '1-90' ) {
	$bingo_card_words = LBCG_Helper::get_1_90_bingo_card_numbers();
} else {
	// Get bingo card words
	if ( ! empty( $data['bingo_card_content'][0] ) ) {
		$bingo_card_content = $data['bingo_card_content'][0];
	} else {
		$result             = LBCG_Helper::get_bg_default_content( $bingo_grid_size );
		$bingo_card_content = $result['words'];
	}
	$bingo_card_words = explode( "\r\n", $bingo_card_content );
}
// Grid square checked style
if ( ! empty( $data['grid_square'] ) ) {
	$grid_square = unserialize( $data['grid_square'][0] );
} else {
	$grid_square = [
		'font_color' => '#ffffff',
		'color'      => '#000'
	];
}
// Header style
if ( ! empty( $data['bc_header'][0] ) ) {
	$bc_header = unserialize( $data['bc_header'][0] );
} else {
	$bc_header = [
		'font_color' => '#ffffff',
		'color'      => '#d6be89',
		'image'      => '',
		'opacity'    => 0,
		'repeat'     => 'no-repeat'
	];
}
// Grid style
if ( ! empty( $data['bc_grid'][0] ) ) {
	$bc_grid = unserialize( $data['bc_grid'][0] );
} else {
	$bc_grid = [
		'font_color'   => '#000',
		'border_color' => '#000',
		'color'        => '#997d3c',
		'image'        => '',
		'opacity'      => 0,
		'repeat'       => 'no-repeat'
	];
}
// Card style
if ( ! empty( $data['bc_card'][0] ) ) {
	$bc_card = unserialize( $data['bc_card'][0] );
} else {
	$bc_card = [
		'color'   => '#d6be89',
		'image'   => '',
		'opacity' => 0,
		'repeat'  => 'no-repeat'
	];
}
// Wrap words
$bingo_card_wrap_words = ! empty( $data['bingo_card_wrap_words'][0] ) && $data['bingo_card_wrap_words'][0] === 'on' ? true : false;
// If include free space
if ( ! empty( $data['bingo_card_free_square'][0] ) && $data['bingo_card_free_square'][0] === 'on' && $bingo_grid_size !== '4x4' /*|| $bingo_card_type === '1-75'*/ ) {
	$bingo_grid_free_square = true;
} else {
	$bingo_grid_free_square = false;
}
?>
    <div class="lbcg-custom-container">
        <main class="lbcg-parent lbcg-loading">
			<?php LBCG_Helper::show_bingo_theme_breadcrumb( $lbcg_current_theme_name, $current_id ); ?>
            <div class="lbcg-post-header">
                <h1><?php the_title(); ?></h1>
            </div>
			<?php if ( ! empty( $data['bt_intro_text'][0] ) ): ?>
                <div class="lbcg-post-content lbcg-intro-text"><?php echo $data['bt_intro_text'][0]; ?></div>
			<?php endif; ?>
            <div class="lbcg-main">
                <aside class="lbcg-sidebar">
					<?php
					$bingo_themes = get_posts( [
						'post_type'   => 'bingo_theme',
						'post_status' => 'publish',
						'numberposts' => - 1,
						'orderby'     => 'post_title',
						'order'       => 'ASC'
					] );
					$menu_items   = [];
					foreach ( $bingo_themes as $bingo_theme ) {
						$bt_categories = get_the_terms( $bingo_theme->ID, 'ubud-category' );
						if ( ! empty( $bt_categories[0] ) ) {
							$menu_items[ $bt_categories[0]->name ][] = $bingo_theme;
						} else {
							$menu_items['uncategorized'][] = $bingo_theme;
						}
					}
					ksort( $menu_items );
					foreach ( $menu_items as $bt_category_name => $menu_item ) {
						?>
                        <div class="lbcg-sidebar-in collapsed">
                            <div class="lbcg-sidebar-header">
                                <a href="#" class="lbcg-sidebar-btn"><?php echo $bt_category_name; ?></a>
                                <span class="lbcg-sidebar-arrow"></span>
                            </div>
                            <div class="lbcg-sidebar-body">
								<?php
								foreach ( $menu_item as $bingo_theme ) {
									?>
                                    <a href="<?php echo esc_url( get_permalink( $bingo_theme->ID ) ); ?>"
                                       class="lbcg-sidebar-link <?php echo $current_id === $bingo_theme->ID ? 'active' : ''; ?>"><?php echo $bingo_theme->post_title; ?></a>
									<?php
								}
								?>
                            </div>
                        </div>
						<?php
					}
					?>
                </aside>

                <section class="lbcg-content">
                    <div class="lbcg-content-left">
                        <form id="lbcg-bc-generation" action="<?php echo admin_url( 'admin-ajax.php' ); ?>"
                              method="post" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="lbcg_bc_generation">
                            <input type="hidden" name="bingo_theme_id" value="<?php echo $current_id; ?>">
                            <input type="hidden" name="bingo_card_type" value="<?php echo $bingo_card_type; ?>">
							<?php if ( ! empty( $bc_posts[0]->ID ) ): ?>
                                <input type="hidden" name="bc" value="<?php echo $_GET['bc']; ?>">
							<?php endif; ?>
                            <div class="lbcg-content-form">
                                <div class="lbcg-input-wrap">
                                    <label for="lbcg-title" class="lbcg-label">Enter a Title</label>
                                    <input class="lbcg-input" id="lbcg-title" type="text" name="bingo_card_title"
                                           value="<?php echo ! empty( $data['bingo_card_title'][0] ) ? $data['bingo_card_title'][0] : ''; ?>"/>
                                </div>
                                <div class="lbcg-input-wrap" <?php echo ! ( ! empty( $data['bingo_card_type'][0] ) && $data['bingo_card_type'][0] === '1-75' ) ? 'style="display: none;"' : ''; ?>>
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
                                <div class="lbcg-input-wrap" <?php echo ! ( ! empty( $data['bingo_card_type'][0] ) && $data['bingo_card_type'][0] !== '1-75' && $data['bingo_card_type'][0] !== '1-90' )
									? 'style="display: none;' : ''; ?>>
                                    <label for="lbcg-body-content" class="lbcg-label">Enter words/emojis or
                                        numbers</label>
                                    <textarea class="lbcg-input" id="lbcg-body-content" name="bingo_card_content"
                                              cols=""
                                              rows="11"><?php echo ! empty( $data['bingo_card_content'][0] ) ? $data['bingo_card_content'][0] : ''; ?></textarea>
                                </div>
								<?php if ( $bingo_card_type !== '1-75' && $bingo_card_type !== '1-90' ): ?>
                                    <div class="lbcg-input-wrap">
                                        <label for="lbcg-grid-size" class="lbcg-label">Select Grid Size</label>
                                        <select name="bingo_grid_size" id="lbcg-grid-size" class="lbcg-select">
                                            <option value="3x3" <?php echo $bingo_grid_size === '3x3' ? 'selected' : ''; ?>>3x3</option>
                                            <option value="4x4" <?php echo $bingo_grid_size === '4x4' ? 'selected' : ''; ?>>4x4</option>
                                            <option value="5x5" <?php echo $bingo_grid_size === '5x5' ? 'selected' : ''; ?>>5x5</option>
                                        </select>
                                    </div>
								<?php else: ?>
                                    <input type="hidden" id="lbcg-grid-size" name="bingo_grid_size" value="<?php echo $bingo_card_type === '1-75' ? '5x5' : '9x3' ?>">
								<?php endif; ?>
                                <div class="lbcg-input-wrap">
                                    <label for="lbcg-font" class="lbcg-label">Select Font Family</label>
                                    <select name="bingo_card_font" id="lbcg-font" class="lbcg-select">
										<?php foreach ( LBCG_Helper::$fonts as $key => $font ): ?>
                                            <option value="<?php echo $key; ?>" <?php echo ! empty( $data['bingo_card_font'][0] ) && $data['bingo_card_font'][0] === $key ? 'selected="selected"' : ''; ?>><?php
												echo $font['name']; ?></option>
										<?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="lbcg-input-wrap" <?php echo ( $bingo_card_type === '1-75' || $bingo_card_type === '1-90' ) ? 'style="display: none;"' : ''; ?>>
                                    <input type="checkbox" class="lbcg-checkbox" id="lbcg-wrap-words-check"
                                           name="bingo_card_wrap_words"
                                           hidden <?php echo $bingo_card_wrap_words ? 'checked' : ''; ?>/>
                                    <label for="lbcg-wrap-words-check" class="lbcg-checkbox-holder"></label>
                                    <label for="lbcg-wrap-words-check" class="lbcg-label">Wrap Words?</label>
                                </div>
                                <div class="lbcg-input-wrap" <?php echo ( /*$bingo_card_type === '1-75' ||*/ $bingo_card_type === '1-90' || $bingo_grid_size === '4x4' ) ? 'style="display: none;"' : ''; ?>>
                                    <input type="checkbox" class="lbcg-checkbox" id="lbcg-free-space-check"
                                           name="bingo_card_free_square"
                                           hidden <?php echo $bingo_grid_free_square ? 'checked' : ''; ?>/>
                                    <label for="lbcg-free-space-check" class="lbcg-checkbox-holder"></label>
                                    <label for="lbcg-free-space-check" class="lbcg-label">Include free space?</label>
                                </div>
                                <div class="lbcg-input-wrap">
                                    <input type="checkbox" class="lbcg-checkbox lbcg-checkbox--collapse" id="lbcg-toggle-square-style-check" hidden/>
                                    <label for="lbcg-toggle-square-style-check" class="lbcg-checkbox-holder"></label>
                                    <label for="lbcg-toggle-square-style-check" class="lbcg-label">Checked Square</label>
                                    <div class="lbcg-input-wrap-in lbcg-input-wrap--collapse">
                                        <div class="lbcg-input-wrap">
                                            <label for="grid-square-font-color" class="lbcg-label">Font Color</label>
                                            <input type="color" id="grid-square-font-color"
                                                   class="bc-font-color lbcg-input" name="grid_square[font_color]"
                                                   value="<?php echo $grid_square['font_color'] ?? '#ffffff'; ?>"
                                                   data-bct="grid-square">
                                        </div>
                                        <div class="lbcg-input-wrap">
                                            <label for="grid-square-bg-color" class="lbcg-label">Background Color</label>
                                            <input type="color" id="grid-square-bg-color" class="bc-color lbcg-input"
                                                   name="grid_square[color]" value="<?php echo $grid_square['color'] ?? '#000'; ?>"
                                                   data-bct="grid-square">
                                        </div>
                                    </div>
                                </div>
                                <div class="lbcg-input-wrap">
                                    <input type="checkbox" class="lbcg-checkbox lbcg-checkbox--collapse"
                                           id="lbcg-header-bg-check" hidden/>
                                    <label for="lbcg-header-bg-check" class="lbcg-checkbox-holder"></label>
                                    <label for="lbcg-header-bg-check" class="lbcg-label">Header Background</label>
                                    <div class="lbcg-input-wrap-in lbcg-input-wrap--collapse">
                                        <div class="lbcg-input-wrap">
                                            <label for="bc-header-font-color" class="lbcg-label">Font Color</label>
                                            <input type="color" id="bc-header-font-color"
                                                   class="bc-font-color lbcg-input" name="bc_header[font_color]"
                                                   value="<?php echo $bc_header['font_color'] ?? '#ffffff'; ?>"
                                                   data-bct="header">
                                        </div>
                                        <div class="lbcg-input-wrap">
                                            <label for="bc-header-color" class="lbcg-label">Background Color</label>
                                            <input type="color" id="bc-header-color" class="bc-color lbcg-input"
                                                   name="bc_header[color]" value="<?php echo $bc_header['color']; ?>"
                                                   data-bct="header">
                                        </div>
                                        <div class="lbcg-input-wrap">
                                            <label for="bc-header-image" class="lbcg-label">Image</label>
                                            <input hidden type="file" accept="image/*" id="bc-header-image"
                                                   class="bc-image lbcg-input" name="bc_header[image]"
                                                   value="<?php echo ! empty( $bc_header['image'] ) ? $bc_header['image'] : '0'; ?>"
                                                   data-bct="header">
                                            <label for="bc-header-image" class="lbcg-btn lbcg-btn--main">Choose
                                                File</label>
                                            <button class="lbcg-btn lbcg-btn--remove remove-bc-image"
                                                    data-bct="header"></button>
                                            <input type="hidden" name="bc_header[remove_image]" value="0">
                                        </div>
                                        <div class="lbcg-input-wrap">
                                            <label for="bc-header-opacity" class="lbcg-label">Opacity (%)</label>
                                            <input type="number" id="bc-header-opacity"
                                                   class="bc-opacity lbcg-input lbcg-input--opacity"
                                                   name="bc_header[opacity]" min="0" max="100" placeholder="0-100"
                                                   value="<?php echo $bc_header['opacity']; ?>" data-bct="header">
                                        </div>
                                        <div class="lbcg-input-wrap">
                                            <label class="lbcg-label">
                                                <select name="bc_header[bg_pos]" class="bc-pos lbcg-select"
                                                        data-bct="header">
                                                    <option value="0 0" <?php echo ! empty( $bc_header['bg_pos'] ) && $bc_header['bg_pos'] === '0 0' ? 'selected' : ''; ?>>
                                                        Background Position
                                                    </option>
                                                    <option value="top left" <?php echo ! empty( $bc_header['bg_pos'] ) && $bc_header['bg_pos'] === 'top left' ? 'selected' : ''; ?>>
                                                        Top Left
                                                    </option>
                                                    <option value="top center" <?php echo ! empty( $bc_header['bg_pos'] ) && $bc_header['bg_pos'] === 'top center' ? 'selected' : ''; ?>>
                                                        Top Center
                                                    </option>
                                                    <option value="top right" <?php echo ! empty( $bc_header['bg_pos'] ) && $bc_header['bg_pos'] === 'top right' ? 'selected' : ''; ?>>
                                                        Top Right
                                                    </option>
                                                    <option value="center left" <?php echo ! empty( $bc_header['bg_pos'] ) && $bc_header['bg_pos'] === 'center left' ? 'selected' : ''; ?>>
                                                        Center Left
                                                    </option>
                                                    <option value="center center" <?php echo ! empty( $bc_header['bg_pos'] ) && $bc_header['bg_pos'] === 'center center' ? 'selected' : ''; ?>>
                                                        Center Center
                                                    </option>
                                                    <option value="center right" <?php echo ! empty( $bc_header['bg_pos'] ) && $bc_header['bg_pos'] === 'center right' ? 'selected' : ''; ?>>
                                                        Center Right
                                                    </option>
                                                    <option value="bottom left" <?php echo ! empty( $bc_header['bg_pos'] ) && $bc_header['bg_pos'] === 'bottom left' ? 'selected' : ''; ?>>
                                                        Bottom Left
                                                    </option>
                                                    <option value="bottom center" <?php echo ! empty( $bc_header['bg_pos'] ) && $bc_header['bg_pos'] === 'bottom center' ? 'selected' : ''; ?>>
                                                        Bottom Center
                                                    </option>
                                                    <option value="bottom right" <?php echo ! empty( $bc_header['bg_pos'] ) && $bc_header['bg_pos'] === 'bottom right' ? 'selected' : ''; ?>>
                                                        Bottom Right
                                                    </option>
                                                </select>
                                            </label>
                                        </div>
                                        <div class="lbcg-input-wrap">
                                            <label class="lbcg-label">
                                                <select name="bc_header[repeat]" class="bc-repeat lbcg-select"
                                                        data-bct="header">
                                                    <option value="no-repeat" <?php echo ! empty( $bc_header['repeat'] ) && $bc_header['repeat'] === 'no-repeat' ? 'selected' : ''; ?>>
                                                        Background Repeat
                                                    </option>
                                                    <option value="repeat" <?php echo ! empty( $bc_header['repeat'] ) && $bc_header['repeat'] === 'repeat' ? 'selected' : ''; ?>>
                                                        Repeat
                                                    </option>
                                                    <option value="repeat-x" <?php echo ! empty( $bc_header['repeat'] ) && $bc_header['repeat'] === 'repeat-x' ? 'selected' : ''; ?>>
                                                        Repeat X
                                                    </option>
                                                    <option value="repeat-y" <?php echo ! empty( $bc_header['repeat'] ) && $bc_header['repeat'] === 'repeat-y' ? 'selected' : ''; ?>>
                                                        Repeat Y
                                                    </option>
                                                    <option value="no-repeat" <?php echo ! empty( $bc_header['repeat'] ) && $bc_header['repeat'] === 'no-repeat' ? 'selected' : ''; ?>>
                                                        No Repeat
                                                    </option>
                                                </select>
                                            </label>
                                        </div>
                                        <div class="lbcg-input-wrap">
                                            <label class="lbcg-label">
                                                <select name="bc_header[bg_size]" class="bc-size lbcg-select"
                                                        data-bct="header">
                                                    <option value="auto" <?php echo ! empty( $bc_header['bg_size'] ) && $bc_header['bg_size'] === 'auto' ? 'selected' : ''; ?>>
                                                        Background Size
                                                    </option>
                                                    <option value="auto" <?php echo ! empty( $bc_header['bg_size'] ) && $bc_header['bg_size'] === 'auto' ? 'selected' : ''; ?>>
                                                        Auto
                                                    </option>
                                                    <option value="contain" <?php echo ! empty( $bc_header['bg_size'] ) && $bc_header['bg_size'] === 'contain' ? 'selected' : ''; ?>>
                                                        Contain
                                                    </option>
                                                    <option value="cover" <?php echo ! empty( $bc_header['bg_size'] ) && $bc_header['bg_size'] === 'cover' ? 'selected' : ''; ?>>
                                                        Cover
                                                    </option>
                                                </select>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="lbcg-input-wrap">
                                    <input type="checkbox" class="lbcg-checkbox lbcg-checkbox--collapse"
                                           id="lbcg-grid-bg-check" hidden/>
                                    <label for="lbcg-grid-bg-check" class="lbcg-checkbox-holder"></label>
                                    <label for="lbcg-grid-bg-check" class="lbcg-label">Grid Background</label>
                                    <div class="lbcg-input-wrap-in lbcg-input-wrap--collapse">
                                        <div class="lbcg-input-wrap">
                                            <label for="bc-grid-font-color" class="lbcg-label">Font Color</label>
                                            <input type="color" id="bc-grid-font-color" class="bc-font-color lbcg-input"
                                                   name="bc_grid[font_color]"
                                                   value="<?php echo $bc_grid['font_color'] ?? '#000'; ?>"
                                                   data-bct="grid">
                                        </div>
                                        <div class="lbcg-input-wrap">
                                            <label for="bc-grid-border-color" class="lbcg-label">Border Color</label>
                                            <input type="color" id="bc-grid-border-color"
                                                   class="bc-border-color lbcg-input" name="bc_grid[border_color]"
                                                   value="<?php echo $bc_grid['border_color'] ?? '#000'; ?>"
                                                   data-bct="grid">
                                        </div>
                                        <div class="lbcg-input-wrap">
                                            <label for="bc-grid-color" class="lbcg-label">Background Color</label>
                                            <input type="color" id="bc-grid-color" class="bc-color lbcg-input"
                                                   name="bc_grid[color]" value="<?php echo $bc_grid['color']; ?>"
                                                   data-bct="grid">
                                        </div>
                                        <div class="lbcg-input-wrap">
                                            <label for="bc-grid-image" class="lbcg-label">Image</label>
                                            <input hidden type="file" accept="image/*" id="bc-grid-image"
                                                   class="bc-image lbcg-input" name="bc_grid[image]"
                                                   value="<?php echo ! empty( $bc_grid['image'] ) ? $bc_grid['image'] : '0'; ?>"
                                                   data-bct="grid">
                                            <label for="bc-grid-image" class="lbcg-btn lbcg-btn--main">Choose
                                                File</label>
                                            <button class="remove-bc-image lbcg-btn lbcg-btn--remove"
                                                    data-bct="grid"></button>
                                            <input type="hidden" name="bc_grid[remove_image]" value="0">
                                        </div>
                                        <div class="lbcg-input-wrap">
                                            <label for="bc-grid-opacity" class="lbcg-label">Opacity (%)</label>
                                            <input type="number" id="bc-grid-opacity"
                                                   class="bc-opacity lbcg-input lbcg-input--opacity"
                                                   name="bc_grid[opacity]" min="0" max="100" placeholder="0-100"
                                                   value="<?php echo $bc_grid['opacity']; ?>" data-bct="grid">
                                        </div>
                                        <div class="lbcg-input-wrap">
                                            <label class="lbcg-label">
                                                <select name="bc_grid[bg_pos]" class="bc-pos lbcg-select"
                                                        data-bct="grid">
                                                    <option value="0 0" <?php echo ! empty( $bc_grid['bg_pos'] ) && $bc_grid['bg_pos'] === '0 0' ? 'selected' : ''; ?>>
                                                        Background Position
                                                    </option>
                                                    <option value="top left" <?php echo ! empty( $bc_grid['bg_pos'] ) && $bc_grid['bg_pos'] === 'top left' ? 'selected' : ''; ?>>
                                                        Top Left
                                                    </option>
                                                    <option value="top center" <?php echo ! empty( $bc_grid['bg_pos'] ) && $bc_grid['bg_pos'] === 'top center' ? 'selected' : ''; ?>>
                                                        Top Center
                                                    </option>
                                                    <option value="top right" <?php echo ! empty( $bc_grid['bg_pos'] ) && $bc_grid['bg_pos'] === 'top right' ? 'selected' : ''; ?>>
                                                        Top Right
                                                    </option>
                                                    <option value="center left" <?php echo ! empty( $bc_grid['bg_pos'] ) && $bc_grid['bg_pos'] === 'center left' ? 'selected' : ''; ?>>
                                                        Center Left
                                                    </option>
                                                    <option value="center center" <?php echo ! empty( $bc_grid['bg_pos'] ) && $bc_grid['bg_pos'] === 'center center' ? 'selected' : ''; ?>>
                                                        Center Center
                                                    </option>
                                                    <option value="center right" <?php echo ! empty( $bc_grid['bg_pos'] ) && $bc_grid['bg_pos'] === 'center right' ? 'selected' : ''; ?>>
                                                        Center Right
                                                    </option>
                                                    <option value="bottom left" <?php echo ! empty( $bc_grid['bg_pos'] ) && $bc_grid['bg_pos'] === 'bottom left' ? 'selected' : ''; ?>>
                                                        Bottom Left
                                                    </option>
                                                    <option value="bottom center" <?php echo ! empty( $bc_grid['bg_pos'] ) && $bc_grid['bg_pos'] === 'bottom center' ? 'selected' : ''; ?>>
                                                        Bottom Center
                                                    </option>
                                                    <option value="bottom right" <?php echo ! empty( $bc_grid['bg_pos'] ) && $bc_grid['bg_pos'] === 'bottom right' ? 'selected' : ''; ?>>
                                                        Bottom Right
                                                    </option>
                                                </select>
                                            </label>
                                        </div>
                                        <div class="lbcg-input-wrap">
                                            <label class="lbcg-label">
                                                <select name="bc_grid[repeat]" class="bc-repeat lbcg-select"
                                                        data-bct="grid">
                                                    <option value="no-repeat" <?php echo ! empty( $bc_grid['repeat'] ) && $bc_grid['repeat'] === 'no-repeat' ? 'selected' : ''; ?>>
                                                        Background Repeat
                                                    </option>
                                                    <option value="repeat" <?php echo ! empty( $bc_grid['repeat'] ) && $bc_grid['repeat'] === 'repeat' ? 'selected' : ''; ?>>
                                                        Repeat
                                                    </option>
                                                    <option value="repeat-x" <?php echo ! empty( $bc_grid['repeat'] ) && $bc_grid['repeat'] === 'repeat-x' ? 'selected' : ''; ?>>
                                                        Repeat X
                                                    </option>
                                                    <option value="repeat-y" <?php echo ! empty( $bc_grid['repeat'] ) && $bc_grid['repeat'] === 'repeat-y' ? 'selected' : ''; ?>>
                                                        Repeat Y
                                                    </option>
                                                    <option value="no-repeat" <?php echo ! empty( $bc_grid['repeat'] ) && $bc_grid['repeat'] === 'no-repeat' ? 'selected' : ''; ?>>
                                                        No Repeat
                                                    </option>
                                                </select>
                                            </label>
                                        </div>
                                        <div class="lbcg-input-wrap">
                                            <label class="lbcg-label">
                                                <select name="bc_grid[bg_size]" class="bc-size lbcg-select"
                                                        data-bct="grid">
                                                    <option value="auto" <?php echo ! empty( $bc_grid['bg_size'] ) && $bc_grid['bg_size'] === 'auto' ? 'selected' : ''; ?>>
                                                        Background Size
                                                    </option>
                                                    <option value="auto" <?php echo ! empty( $bc_grid['bg_size'] ) && $bc_grid['bg_size'] === 'auto' ? 'selected' : ''; ?>>
                                                        Auto
                                                    </option>
                                                    <option value="contain" <?php echo ! empty( $bc_grid['bg_size'] ) && $bc_grid['bg_size'] === 'contain' ? 'selected' : ''; ?>>
                                                        Contain
                                                    </option>
                                                    <option value="cover" <?php echo ! empty( $bc_grid['bg_size'] ) && $bc_grid['bg_size'] === 'cover' ? 'selected' : ''; ?>>
                                                        Cover
                                                    </option>
                                                </select>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="lbcg-input-wrap">
                                    <input type="checkbox" class="lbcg-checkbox lbcg-checkbox--collapse"
                                           id="lbcg-card-bg-check" hidden/>
                                    <label for="lbcg-card-bg-check" class="lbcg-checkbox-holder"></label>
                                    <label for="lbcg-card-bg-check" class="lbcg-label">Card Background</label>
                                    <div class="lbcg-input-wrap-in lbcg-input-wrap--collapse">
                                        <div class="lbcg-input-wrap">
                                            <label for="bc-card-color" class="lbcg-label">Background Color</label>
                                            <input type="color" id="bc-card-color" class="bc-color lbcg-input"
                                                   name="bc_card[color]" value="<?php echo $bc_card['color']; ?>"
                                                   data-bct="card">
                                        </div>
                                        <div class="lbcg-input-wrap">
                                            <label for="bc-card-image" class="lbcg-label">Image</label>
                                            <input hidden type="file" accept="image/*" id="bc-card-image"
                                                   class="bc-image lbcg-input" name="bc_card[image]"
                                                   value="<?php echo ! empty( $bc_card['image'] ) ? $bc_card['image'] : '0'; ?>"
                                                   data-bct="card">
                                            <label for="bc-card-image" class="lbcg-btn lbcg-btn--main">Choose
                                                File</label>
                                            <button class="remove-bc-image lbcg-btn lbcg-btn--remove"
                                                    data-bct="card"></button>
                                            <input type="hidden" name="bc_card[remove_image]" value="0">
                                        </div>
                                        <div class="lbcg-input-wrap">
                                            <label for="bc-card-opacity" class="lbcg-label">Opacity (%)</label>
                                            <input type="number" id="bc-card-opacity"
                                                   class="bc-opacity lbcg-input lbcg-input--opacity"
                                                   name="bc_card[opacity]" min="0" max="100" placeholder="0-100"
                                                   value="<?php echo $bc_card['opacity']; ?>" data-bct="card">
                                        </div>
                                        <div class="lbcg-input-wrap">
                                            <label class="lbcg-label">
                                                <select name="bc_card[bg_pos]" class="bc-pos lbcg-select"
                                                        data-bct="card">
                                                    <option value="0 0" <?php echo ! empty( $bc_card['bg_pos'] ) && $bc_card['bg_pos'] === '0 0' ? 'selected' : ''; ?>>
                                                        Background Position
                                                    </option>
                                                    <option value="top left" <?php echo ! empty( $bc_card['bg_pos'] ) && $bc_card['bg_pos'] === 'top left' ? 'selected' : ''; ?>>
                                                        Top Left
                                                    </option>
                                                    <option value="top center" <?php echo ! empty( $bc_card['bg_pos'] ) && $bc_card['bg_pos'] === 'top center' ? 'selected' : ''; ?>>
                                                        Top Center
                                                    </option>
                                                    <option value="top right" <?php echo ! empty( $bc_card['bg_pos'] ) && $bc_card['bg_pos'] === 'top right' ? 'selected' : ''; ?>>
                                                        Top Right
                                                    </option>
                                                    <option value="center left" <?php echo ! empty( $bc_card['bg_pos'] ) && $bc_card['bg_pos'] === 'center left' ? 'selected' : ''; ?>>
                                                        Center Left
                                                    </option>
                                                    <option value="center center" <?php echo ! empty( $bc_card['bg_pos'] ) && $bc_card['bg_pos'] === 'center center' ? 'selected' : ''; ?>>
                                                        Center Center
                                                    </option>
                                                    <option value="center right" <?php echo ! empty( $bc_card['bg_pos'] ) && $bc_card['bg_pos'] === 'center right' ? 'selected' : ''; ?>>
                                                        Center Right
                                                    </option>
                                                    <option value="bottom left" <?php echo ! empty( $bc_card['bg_pos'] ) && $bc_card['bg_pos'] === 'bottom left' ? 'selected' : ''; ?>>
                                                        Bottom Left
                                                    </option>
                                                    <option value="bottom center" <?php echo ! empty( $bc_card['bg_pos'] ) && $bc_card['bg_pos'] === 'bottom center' ? 'selected' : ''; ?>>
                                                        Bottom Center
                                                    </option>
                                                    <option value="bottom right" <?php echo ! empty( $bc_card['bg_pos'] ) && $bc_card['bg_pos'] === 'bottom right' ? 'selected' : ''; ?>>
                                                        Bottom Right
                                                    </option>
                                                </select>
                                            </label>
                                        </div>
                                        <div class="lbcg-input-wrap">
                                            <label class="lbcg-label">
                                                <select name="bc_card[repeat]" class="bc-repeat lbcg-select"
                                                        data-bct="card">
                                                    <option value="no-repeat" <?php echo ! empty( $bc_card['repeat'] ) && $bc_card['repeat'] === 'no-repeat' ? 'selected' : ''; ?>>
                                                        Background Repeat
                                                    </option>
                                                    <option value="repeat" <?php echo ! empty( $bc_card['repeat'] ) && $bc_card['repeat'] === 'repeat' ? 'selected' : ''; ?>>
                                                        Repeat
                                                    </option>
                                                    <option value="repeat-x" <?php echo ! empty( $bc_card['repeat'] ) && $bc_card['repeat'] === 'repeat-x' ? 'selected' : ''; ?>>
                                                        Repeat X
                                                    </option>
                                                    <option value="repeat-y" <?php echo ! empty( $bc_card['repeat'] ) && $bc_card['repeat'] === 'repeat-y' ? 'selected' : ''; ?>>
                                                        Repeat Y
                                                    </option>
                                                    <option value="no-repeat" <?php echo ! empty( $bc_card['repeat'] ) && $bc_card['repeat'] === 'no-repeat' ? 'selected' : ''; ?>>
                                                        No Repeat
                                                    </option>
                                                </select>
                                            </label>
                                        </div>
                                        <div class="lbcg-input-wrap">
                                            <label class="lbcg-label">
                                                <select name="bc_card[bg_size]" class="bc-size lbcg-select"
                                                        data-bct="card">
                                                    <option value="auto" <?php echo ! empty( $bc_card['bg_size'] ) && $bc_card['bg_size'] === 'auto' ? 'selected' : ''; ?>>
                                                        Background Size
                                                    </option>
                                                    <option value="auto" <?php echo ! empty( $bc_card['bg_size'] ) && $bc_card['bg_size'] === 'auto' ? 'selected' : ''; ?>>
                                                        Auto
                                                    </option>
                                                    <option value="contain" <?php echo ! empty( $bc_card['bg_size'] ) && $bc_card['bg_size'] === 'contain' ? 'selected' : ''; ?>>
                                                        Contain
                                                    </option>
                                                    <option value="cover" <?php echo ! empty( $bc_card['bg_size'] ) && $bc_card['bg_size'] === 'cover' ? 'selected' : ''; ?>>
                                                        Cover
                                                    </option>
                                                </select>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="lbcg-input-wrap">
                                <button class="lbcg-btn lbcg-btn--lg lbcg-btn--main" type="submit">Generate Bingo Card
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="lbcg-content-right">
                        <div class="lbcg-card-wrap">
                            <div class="lbcg-card">
                                <div class="lbcg-card-header-holder">
                                    <div class="lbcg-card-header">
                                        <span class="lbcg-card-header-text"><?php echo ! empty( $data['bingo_card_title'][0] ) ? $data['bingo_card_title'][0] : ''; ?></span>
                                    </div>
									<?php if ( $bingo_card_type === '1-75' ): ?>
                                        <div class="lbcg-card-subtitle">
                                            <span class="lbcg-card-subtitle-text"><span><?php echo ! empty( $bingo_card_spec_title ) ? implode( '</span><span>', $bingo_card_spec_title ) : ''; ?></span></span>
                                        </div>
									<?php endif; ?>
                                </div>
                                <div class="lbcg-card-body">
									<?php if ( $bingo_card_type === '1-90' ) {
										foreach ( $bingo_card_words as $single_card_words ) { ?>
                                            <div class="lbcg-card-body-grid lbcg-grid-9">
												<?php
												foreach ( $single_card_words as $number ) { ?>
                                                    <div class="lbcg-card-col">
                                                        <span class="lbcg-card-text"><?php echo $number; ?></span>
                                                    </div>
												<?php } ?>
                                            </div>
											<?php
										}
									} else { ?>
                                        <div class="lbcg-card-body-grid lbcg-grid-<?php echo $bingo_grid_size[0]; ?>">
											<?php
											$grid_sq_count = $bingo_grid_size[0] ** 2;
											for ( $i = 1; $i <= $grid_sq_count; $i ++ ): ?>
                                                <div class="lbcg-card-col">
                                            <span class="lbcg-card-text"><?php
	                                            if ( (int) ceil( $grid_sq_count / 2 ) === $i && $bingo_grid_free_square ) {
		                                            echo LBCG_Helper::$free_space_word;
	                                            } else {
		                                            echo $bingo_card_words[ $i - 1 ];
	                                            }
	                                            ?></span>
                                                </div>
											<?php endfor; ?>
                                        </div>
									<?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
			<?php if ( $the_content = get_the_content() ): ?>
                <div class="lbcg-post-content"><?php echo $the_content; ?></div>
			<?php endif; ?>
        </main>
    </div>
<?php
if ( $lbcg_current_theme_name === 'BNBS' ) {
	$data = array( 'footer' => array() );
	require( get_template_directory() . '/models/m-partials.php' );
	$footer = $data['footer'];
	require( get_template_directory() . '/footer.php' );
} else {
	get_footer();
}