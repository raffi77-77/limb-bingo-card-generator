<?php
$data             = LBCG_Admin::get_instance()->get_post_data();
$bingo_card_type  = ! empty( $data['bingo_card_type'][0] ) ? $data['bingo_card_type'][0] : 'generic';
$bingo_grid_size  = ! empty( $data['bingo_grid_size'][0] ) ? $data['bingo_grid_size'][0] : '3x3';
$bingo_card_title = ! empty( $data['bingo_card_title'][0] ) ? $data['bingo_card_title'][0] : 'Card Title';
// Special title
if ( ! empty( $data['bingo_card_spec_title'][0] ) ) {
	$bingo_card_spec_title = str_split( $data['bingo_card_spec_title'][0] );
} else {
	$bingo_card_spec_title = [ 'B', 'I', 'N', 'G', 'O' ];
}
$additional_spec_part = '';
switch ( count( $bingo_card_spec_title ) ) {
	case 4:
	case 3:
		$additional_spec_part = '<span></span>';
		break;
	case 2:
	case 1:
		$additional_spec_part = '<span></span><span></span>';
		break;
}
// Bingo card words/percents
$bingo_card_words_percents = [];
$words_count               = 25;
$bingo_card_content        = "1\n16\n31\n46\n61\n2\n17\n32\n47\n62\n3\n18\n33\n48\n63\n4\n19\n34\n49\n64\n5\n20\n35\n50\n65";
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
	if ( strpos( $bingo_card_content, "\r\n" ) !== false ) {
		$bingo_card_words = explode( "\r\n", $bingo_card_content );
	} else {
		$bingo_card_words = explode( "\n", $bingo_card_content );
	}
	$words_count               = count( $bingo_card_words );
	$bingo_card_words_percents = ! empty( $data['bcc_words_percents'][0] ) ? unserialize( $data['bcc_words_percents'][0] ) : [];
}
// Need words count
if ( $bingo_grid_size === '3x3' ) {
	$min_words_count = 9;
} elseif ( $bingo_grid_size === '4x4' ) {
	$min_words_count = 16;
} else {
	$min_words_count = 25;
}
if ( ! empty( $data['grid_square'] ) ) {
	$grid_square = unserialize( $data['grid_square'][0] );
} else {
	$grid_square = [
		'font_color' => '#ffffff',
		'color'      => '#000'
	];
}
if ( ! empty( $data['bc_header'][0] ) ) {
	$bc_header = unserialize( $data['bc_header'][0] );
} else {
	$bc_header = [
		'font_color' => '#ffffff',
		'color'      => '#d6be89',
		'image'      => '',
		'opacity'    => 100,
		'repeat'     => 'no-repeat',
		'bg_pos'     => 'center center',
		'bg_size'    => 'cover'
	];
}
if ( ! empty( $data['bc_grid'][0] ) ) {
	$bc_grid = unserialize( $data['bc_grid'][0] );
} else {
	$bc_grid = [
		'font_color'   => '#000',
		'border_color' => '#000',
		'color'        => '#997d3c',
		'image'        => '',
		'opacity'      => 100,
		'repeat'       => 'no-repeat',
		'bg_pos'       => 'center center',
		'bg_size'      => 'cover'
	];
}
if ( ! empty( $data['bc_card'][0] ) ) {
	$bc_card = unserialize( $data['bc_card'][0] );
} else {
	$bc_card = [
		'color'   => '#d6be89',
		'image'   => '',
		'opacity' => 100,
		'repeat'  => 'no-repeat',
		'bg_pos'  => 'center center',
		'bg_size' => 'cover'
	];
}
// Wrap words
$bingo_card_wrap_words = ! empty( $data['bingo_card_wrap_words'][0] ) && $data['bingo_card_wrap_words'][0] === 'on' ? true : false;
// Even distribution
$bingo_card_even_distribution = ! empty( $data['bingo_card_even_distribution'][0] ) && $data['bingo_card_even_distribution'][0] === 'on' ? true : false;
// If include free space
if ( ! empty( $data['bingo_card_free_square'][0] ) && $data['bingo_card_free_square'][0] === 'on' && $bingo_grid_size !== '4x4' && $bingo_card_type !== '1-90' ) {
	$bingo_grid_free_square = true;
} else {
	$bingo_grid_free_square = false;
}
$special_types = array( '1-75', '1-90' );
?>
<input type="hidden" name="lbcg_action" value="save_bc_post">
<input type="hidden" name="bingo_grid_size" value="<?php echo $bingo_grid_size; ?>">
<input type="hidden" name="lbcg_font_size" value="<?php echo LBCG_Helper::$font_size; ?>">
<input type="hidden" name="bingo_card_thumbnail" value="">
<div class="lbcg-generator-container">
    <main class="lbcg-parent lbcg-loading">
        <div class="lbcg-main lbcg-main-admin">
            <aside>
                <div class="lbcg-input-wrap">
                    <label for="lbcg-bc-type" class="lbcg-label">Select Card Type</label>
                    <select name="bingo_card_type" id="lbcg-bc-type" class="lbcg-select">
                        <option value="generic" <?php echo $bingo_card_type === 'generic' ? 'selected="selected"' : ''; ?>>Generic</option>
                        <option value="1-75" <?php echo $bingo_card_type === '1-75' ? 'selected="selected"' : ''; ?>>1-75</option>
                        <option value="1-90" <?php echo $bingo_card_type === '1-90' ? 'selected="selected"' : ''; ?>>1-90</option>
                    </select>
                </div>
                <div class="lbcg-input-wrap" <?php echo $bingo_card_type === '1-75' || $bingo_card_type === '1-90' ? 'style="display: none;"' : ''; ?>>
                    <label for="lbcg-grid-size" class="lbcg-label">Select Grid Size</label>
                    <select id="lbcg-grid-size" class="lbcg-select">
                        <option value="3x3" <?php echo $bingo_grid_size === '3x3' ? 'selected' : ''; ?>>3x3</option>
                        <option value="4x4" <?php echo $bingo_grid_size === '4x4' ? 'selected' : ''; ?>>4x4</option>
                        <option value="5x5" <?php echo $bingo_grid_size === '5x5' ? 'selected' : ''; ?>>5x5</option>
                    </select>
                </div>
                <div class="lbcg-input-wrap">
                    <label for="bc-custom-css" class="lbcg-label">Custom Style (allowed tags: style, link)</label>
                    <textarea class="lbcg-input" id="bc-custom-css" name="bingo_card_custom_css" cols="" rows="11"><?php echo ! empty( $data['bingo_card_custom_css'][0] ) ? $data['bingo_card_custom_css'][0]
							: ''; ?></textarea>
                </div>
            </aside>
            <section class="lbcg-content">
                <div class="lbcg-content-left">
                    <div class="lbcg-content-form">
                        <div class="lbcg-input-wrap">
                            <label for="lbcg-title" class="lbcg-label">Enter a Title</label>
                            <input class="lbcg-input" id="lbcg-title" type="text" name="bingo_card_title" value="<?php echo $bingo_card_title; ?>"/>
                        </div>
                        <div class="lbcg-input-wrap" <?php echo ! ( ! empty( $data['bingo_card_type'][0] ) && $data['bingo_card_type'][0] === '1-75' ) ? 'style="display: none;"' : ''; ?>>
                            <label for="lbcg-subtitle" class="lbcg-label">Enter a Subtitle</label>
                            <input class="lbcg-input" id="lbcg-subtitle" type="text" name="bingo_card_spec_title" maxlength="5" value="<?php echo implode( '', $bingo_card_spec_title ); ?>"/>
                        </div>
                        <div id="lbcg-even-distribution-content"
                             class="lbcg-input-wrap" <?php echo ( $bingo_card_type === '1-75' || $bingo_card_type === '1-90' || ! $bingo_card_even_distribution ) ? 'style="display: none;"' : ''; ?>>
                            <label class="lbcg-label">Enter frequency of words/emojis or numbers with
                                precents (0 to 100)</label>
		                    <?php if ( $bingo_card_even_distribution ):
			                    foreach ( $bingo_card_words as $key => $word ): ?>
                                    <div class="lbcg-input-wrap-in lbcg-input-wrap--words-distribution">
                                        <label class="lbcg-label lbcg-label--single">
                                            <input class="lbcg-input"
                                                   type="text" value="<?php echo $word; ?>" readonly/>
                                        </label>
                                        <label class="lbcg-label lbcg-label--single">
                                            <input class="lbcg-input lbcg-word-percent"
                                                   name="bcc_words_percents[]" type="number" min="0" max="100"
                                                   step="0.01"
                                                   value="<?php echo $bingo_card_words_percents[ $key ] ?? 0; ?>"
                                                   data-wpi="<?php echo $key; ?>"/>
                                        </label>
                                    </div>
			                    <?php endforeach;
		                    endif; ?>
                        </div>
                        <div id="lbcg-cards-words-content" class="lbcg-input-wrap" <?php echo $bingo_card_type === '1-75' || $bingo_card_type === '1-90' ? 'style="display: none;' : ''; ?>>
                            <label for="lbcg-body-content" class="lbcg-label">Enter words/emojis or numbers</label>
                            <p>Note: Please fill minimum <span id="content-items-count"><?php echo $min_words_count; ?></span> words/emojis or numbers, each in new line.</p>
                            <textarea class="lbcg-input" id="lbcg-body-content" name="bingo_card_content" cols="" rows="11"><?php echo $bingo_card_content; ?></textarea>
                        </div>
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
                            <input type="checkbox" class="lbcg-checkbox" id="lbcg-even-distribution-check"
                                   name="bingo_card_even_distribution"
                                   hidden <?php echo $bingo_card_even_distribution ? 'checked' : ''; ?>/>
                            <label for="lbcg-even-distribution-check" class="lbcg-checkbox-holder"></label>
                            <label for="lbcg-even-distribution-check" class="lbcg-label">Even distribution</label>
                        </div>
                        <div class="lbcg-input-wrap" <?php echo ( $bingo_card_type === '1-75' || $bingo_card_type === '1-90' ) ? 'style="display: none;"' : ''; ?>>
                            <input type="checkbox" class="lbcg-checkbox" id="lbcg-wrap-words-check"
                                   name="bingo_card_wrap_words"
                                   hidden <?php echo $bingo_card_wrap_words ? 'checked' : ''; ?>/>
                            <label for="lbcg-wrap-words-check" class="lbcg-checkbox-holder"></label>
                            <label for="lbcg-wrap-words-check" class="lbcg-label">Wrap Words?</label>
                        </div>
                        <div class="lbcg-input-wrap" <?php echo ( /*$bingo_card_type === '1-75' ||*/
							$bingo_card_type === '1-90' || $bingo_grid_size === '4x4' ) ? 'style="display: none;"' : ''; ?>>
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
                            <input type="checkbox" class="lbcg-checkbox lbcg-checkbox--collapse" id="lbcg-header-bg-check" hidden/>
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
                                    <input type="hidden" id="bc-header-image"
                                           class="lbcg-input" name="bc_header[image]"
                                           value="<?php echo ! empty( $bc_header['image'] ) ? $bc_header['image'] : '0'; ?>">
                                    <label for="bc-header-image" class="lbcg-btn lbcg-btn--main bc-image-admin" data-bct="header">Choose File</label>
                                    <button class="lbcg-btn lbcg-btn--remove remove-bc-image-admin"
                                            data-bct="header"></button>

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
                                            <option value="0 0" <?php echo ! empty( $bc_header['bg_pos'] ) && $bc_header['bg_pos'] === '0 0' ? 'selected' : ''; ?>>Background Position</option>
                                            <option value="top left" <?php echo ! empty( $bc_header['bg_pos'] ) && $bc_header['bg_pos'] === 'top left' ? 'selected' : ''; ?>>Top Left</option>
                                            <option value="top center" <?php echo ! empty( $bc_header['bg_pos'] ) && $bc_header['bg_pos'] === 'top center' ? 'selected' : ''; ?>>Top Center</option>
                                            <option value="top right" <?php echo ! empty( $bc_header['bg_pos'] ) && $bc_header['bg_pos'] === 'top right' ? 'selected' : ''; ?>>Top Right</option>
                                            <option value="center left" <?php echo ! empty( $bc_header['bg_pos'] ) && $bc_header['bg_pos'] === 'center left' ? 'selected' : ''; ?>>Center Left</option>
                                            <option value="center center" <?php echo ! empty( $bc_header['bg_pos'] ) && $bc_header['bg_pos'] === 'center center' ? 'selected' : ''; ?>>Center Center</option>
                                            <option value="center right" <?php echo ! empty( $bc_header['bg_pos'] ) && $bc_header['bg_pos'] === 'center right' ? 'selected' : ''; ?>>Center Right</option>
                                            <option value="bottom left" <?php echo ! empty( $bc_header['bg_pos'] ) && $bc_header['bg_pos'] === 'bottom left' ? 'selected' : ''; ?>>Bottom Left</option>
                                            <option value="bottom center" <?php echo ! empty( $bc_header['bg_pos'] ) && $bc_header['bg_pos'] === 'bottom center' ? 'selected' : ''; ?>>Bottom Center</option>
                                            <option value="bottom right" <?php echo ! empty( $bc_header['bg_pos'] ) && $bc_header['bg_pos'] === 'bottom right' ? 'selected' : ''; ?>>Bottom Right</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="lbcg-input-wrap"<?php echo isset( $bc_header['bg_size'] ) && $bc_header['bg_size'] !== 'contain' ? ' style="display: none;"' : ''; ?>>
                                    <label class="lbcg-label">
                                        <select name="bc_header[repeat]" class="bc-repeat lbcg-select"
                                                data-bct="header">
                                            <option value="no-repeat" <?php echo ! empty( $bc_header['repeat'] ) && $bc_header['repeat'] === 'no-repeat' ? 'selected' : ''; ?>>Background Repeat</option>
                                            <option value="repeat" <?php echo ! empty( $bc_header['repeat'] ) && $bc_header['repeat'] === 'repeat' ? 'selected' : ''; ?>>Repeat: The background image is repeated both vertically and horizontally. The last image will be clipped if it does not fit. This is default</option>
                                            <option value="repeat-x" <?php echo ! empty( $bc_header['repeat'] ) && $bc_header['repeat'] === 'repeat-x' ? 'selected' : ''; ?>>Repeat X: The background image is repeated only horizontally</option>
                                            <option value="repeat-y" <?php echo ! empty( $bc_header['repeat'] ) && $bc_header['repeat'] === 'repeat-y' ? 'selected' : ''; ?>>Repeat Y: The background image is repeated only vertically</option>
                                            <option value="no-repeat" <?php echo ! empty( $bc_header['repeat'] ) && $bc_header['repeat'] === 'no-repeat' ? 'selected' : ''; ?>>No Repeat: The background-image is not repeated. The image will only be shown once</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="lbcg-input-wrap">
                                    <label class="lbcg-label">
                                        <select name="bc_header[bg_size]" class="bc-size lbcg-select"
                                                data-bct="header">
                                            <option value="auto" <?php echo ! empty( $bc_header['bg_size'] ) && $bc_header['bg_size'] === 'auto' ? 'selected' : ''; ?>>Background Size</option>
                                            <option value="auto" <?php echo ! empty( $bc_header['bg_size'] ) && $bc_header['bg_size'] === 'auto' ? 'selected' : ''; ?>>Auto</option>
                                            <option value="contain" <?php echo ! empty( $bc_header['bg_size'] ) && $bc_header['bg_size'] === 'contain' ? 'selected' : ''; ?>>Contain</option>
                                            <option value="cover" <?php echo ! empty( $bc_header['bg_size'] ) && $bc_header['bg_size'] === 'cover' ? 'selected' : ''; ?>>Cover</option>
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
                                    <input type="hidden" id="bc-grid-image"
                                           class="lbcg-input" name="bc_grid[image]"
                                           value="<?php echo ! empty( $bc_grid['image'] ) ? $bc_grid['image'] : '0'; ?>">
                                    <label for="bc-grid-image" class="lbcg-btn lbcg-btn--main bc-image-admin" data-bct="grid">Choose File</label>
                                    <button class="remove-bc-image-admin lbcg-btn lbcg-btn--remove"
                                            data-bct="grid"></button>
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
                                            <option value="0 0" <?php echo ! empty( $bc_grid['bg_pos'] ) && $bc_grid['bg_pos'] === '0 0' ? 'selected' : ''; ?>>Background Position</option>
                                            <option value="top left" <?php echo ! empty( $bc_grid['bg_pos'] ) && $bc_grid['bg_pos'] === 'top left' ? 'selected' : ''; ?>>Top Left</option>
                                            <option value="top center" <?php echo ! empty( $bc_grid['bg_pos'] ) && $bc_grid['bg_pos'] === 'top center' ? 'selected' : ''; ?>>Top Center</option>
                                            <option value="top right" <?php echo ! empty( $bc_grid['bg_pos'] ) && $bc_grid['bg_pos'] === 'top right' ? 'selected' : ''; ?>>Top Right</option>
                                            <option value="center left" <?php echo ! empty( $bc_grid['bg_pos'] ) && $bc_grid['bg_pos'] === 'center left' ? 'selected' : ''; ?>>Center Left</option>
                                            <option value="center center" <?php echo ! empty( $bc_grid['bg_pos'] ) && $bc_grid['bg_pos'] === 'center center' ? 'selected' : ''; ?>>Center Center</option>
                                            <option value="center right" <?php echo ! empty( $bc_grid['bg_pos'] ) && $bc_grid['bg_pos'] === 'center right' ? 'selected' : ''; ?>>Center Right</option>
                                            <option value="bottom left" <?php echo ! empty( $bc_grid['bg_pos'] ) && $bc_grid['bg_pos'] === 'bottom left' ? 'selected' : ''; ?>>Bottom Left</option>
                                            <option value="bottom center" <?php echo ! empty( $bc_grid['bg_pos'] ) && $bc_grid['bg_pos'] === 'bottom center' ? 'selected' : ''; ?>>Bottom Center</option>
                                            <option value="bottom right" <?php echo ! empty( $bc_grid['bg_pos'] ) && $bc_grid['bg_pos'] === 'bottom right' ? 'selected' : ''; ?>>Bottom Right</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="lbcg-input-wrap"<?php echo isset( $bc_grid['bg_size'] ) && $bc_grid['bg_size'] !== 'contain' ? ' style="display: none;"' : ''; ?>>
                                    <label class="lbcg-label">
                                        <select name="bc_grid[repeat]" class="bc-repeat lbcg-select"
                                                data-bct="grid">
                                            <option value="no-repeat" <?php echo ! empty( $bc_grid['repeat'] ) && $bc_grid['repeat'] === 'no-repeat' ? 'selected' : ''; ?>>Background Repeat</option>
                                            <option value="repeat" <?php echo ! empty( $bc_grid['repeat'] ) && $bc_grid['repeat'] === 'repeat' ? 'selected' : ''; ?>>Repeat: The background image is repeated both vertically and horizontally. The last image will be clipped if it does not fit. This is default</option>
                                            <option value="repeat-x" <?php echo ! empty( $bc_grid['repeat'] ) && $bc_grid['repeat'] === 'repeat-x' ? 'selected' : ''; ?>>Repeat X: The background image is repeated only horizontally</option>
                                            <option value="repeat-y" <?php echo ! empty( $bc_grid['repeat'] ) && $bc_grid['repeat'] === 'repeat-y' ? 'selected' : ''; ?>>Repeat Y: The background image is repeated only vertically</option>
                                            <option value="no-repeat" <?php echo ! empty( $bc_grid['repeat'] ) && $bc_grid['repeat'] === 'no-repeat' ? 'selected' : ''; ?>>No Repeat: The background-image is not repeated. The image will only be shown once</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="lbcg-input-wrap">
                                    <label class="lbcg-label">
                                        <select name="bc_grid[bg_size]" class="bc-size lbcg-select"
                                                data-bct="grid">
                                            <option value="auto" <?php echo ! empty( $bc_grid['bg_size'] ) && $bc_grid['bg_size'] === 'auto' ? 'selected' : ''; ?>>Background Size</option>
                                            <option value="auto" <?php echo ! empty( $bc_grid['bg_size'] ) && $bc_grid['bg_size'] === 'auto' ? 'selected' : ''; ?>>Auto</option>
                                            <option value="contain" <?php echo ! empty( $bc_grid['bg_size'] ) && $bc_grid['bg_size'] === 'contain' ? 'selected' : ''; ?>>Contain</option>
                                            <option value="cover" <?php echo ! empty( $bc_grid['bg_size'] ) && $bc_grid['bg_size'] === 'cover' ? 'selected' : ''; ?>>Cover</option>
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
                                    <input type="hidden" id="bc-card-image"
                                           class="lbcg-input" name="bc_card[image]"
                                           value="<?php echo ! empty( $bc_card['image'] ) ? $bc_card['image'] : '0'; ?>">
                                    <label for="bc-card-image" class="lbcg-btn lbcg-btn--main bc-image-admin" data-bct="card">Choose File</label>
                                    <button class="remove-bc-image-admin lbcg-btn lbcg-btn--remove"
                                            data-bct="card"></button>
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
                                            <option value="0 0" <?php echo ! empty( $bc_card['bg_pos'] ) && $bc_card['bg_pos'] === '0 0' ? 'selected' : ''; ?>>Background Position</option>
                                            <option value="top left" <?php echo ! empty( $bc_card['bg_pos'] ) && $bc_card['bg_pos'] === 'top left' ? 'selected' : ''; ?>>Top Left</option>
                                            <option value="top center" <?php echo ! empty( $bc_card['bg_pos'] ) && $bc_card['bg_pos'] === 'top center' ? 'selected' : ''; ?>>Top Center</option>
                                            <option value="top right" <?php echo ! empty( $bc_card['bg_pos'] ) && $bc_card['bg_pos'] === 'top right' ? 'selected' : ''; ?>>Top Right</option>
                                            <option value="center left" <?php echo ! empty( $bc_card['bg_pos'] ) && $bc_card['bg_pos'] === 'center left' ? 'selected' : ''; ?>>Center Left</option>
                                            <option value="center center" <?php echo ! empty( $bc_card['bg_pos'] ) && $bc_card['bg_pos'] === 'center center' ? 'selected' : ''; ?>>Center Center</option>
                                            <option value="center right" <?php echo ! empty( $bc_card['bg_pos'] ) && $bc_card['bg_pos'] === 'center right' ? 'selected' : ''; ?>>Center Right</option>
                                            <option value="bottom left" <?php echo ! empty( $bc_card['bg_pos'] ) && $bc_card['bg_pos'] === 'bottom left' ? 'selected' : ''; ?>>Bottom Left</option>
                                            <option value="bottom center" <?php echo ! empty( $bc_card['bg_pos'] ) && $bc_card['bg_pos'] === 'bottom center' ? 'selected' : ''; ?>>Bottom Center</option>
                                            <option value="bottom right" <?php echo ! empty( $bc_card['bg_pos'] ) && $bc_card['bg_pos'] === 'bottom right' ? 'selected' : ''; ?>>Bottom Right</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="lbcg-input-wrap"<?php echo isset( $bc_card['bg_size'] ) && $bc_card['bg_size'] !== 'contain' ? ' style="display: none;"' : ''; ?>>
                                    <label class="lbcg-label">
                                        <select name="bc_card[repeat]" class="bc-repeat lbcg-select"
                                                data-bct="card">
                                            <option value="no-repeat" <?php echo ! empty( $bc_card['repeat'] ) && $bc_card['repeat'] === 'no-repeat' ? 'selected' : ''; ?>>Background Repeat</option>
                                            <option value="repeat" <?php echo ! empty( $bc_card['repeat'] ) && $bc_card['repeat'] === 'repeat' ? 'selected' : ''; ?>>Repeat: The background image is repeated both vertically and horizontally. The last image will be clipped if it does not fit. This is default</option>
                                            <option value="repeat-x" <?php echo ! empty( $bc_card['repeat'] ) && $bc_card['repeat'] === 'repeat-x' ? 'selected' : ''; ?>>Repeat X: The background image is repeated only horizontally</option>
                                            <option value="repeat-y" <?php echo ! empty( $bc_card['repeat'] ) && $bc_card['repeat'] === 'repeat-y' ? 'selected' : ''; ?>>Repeat Y: The background image is repeated only vertically</option>
                                            <option value="no-repeat" <?php echo ! empty( $bc_card['repeat'] ) && $bc_card['repeat'] === 'no-repeat' ? 'selected' : ''; ?>>No Repeat: The background-image is not repeated. The image will only be shown once</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="lbcg-input-wrap">
                                    <label class="lbcg-label">
                                        <select name="bc_card[bg_size]" class="bc-size lbcg-select"
                                                data-bct="card">
                                            <option value="auto" <?php echo ! empty( $bc_card['bg_size'] ) && $bc_card['bg_size'] === 'auto' ? 'selected' : ''; ?>>Background Size</option>
                                            <option value="auto" <?php echo ! empty( $bc_card['bg_size'] ) && $bc_card['bg_size'] === 'auto' ? 'selected' : ''; ?>>Auto</option>
                                            <option value="contain" <?php echo ! empty( $bc_card['bg_size'] ) && $bc_card['bg_size'] === 'contain' ? 'selected' : ''; ?>>Contain</option>
                                            <option value="cover" <?php echo ! empty( $bc_card['bg_size'] ) && $bc_card['bg_size'] === 'cover' ? 'selected' : ''; ?>>Cover</option>
                                        </select>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="lbcg-content-right">
                    <div class="lbcg-card-wrap">
                        <div class="lbcg-card">
                            <div class="lbcg-card-header-holder">
                                <div class="lbcg-card-header">
                                    <span class="lbcg-card-header-text"><?php echo $bingo_card_title; ?></span>
                                </div>
                                <div class="lbcg-card-subtitle" style="<?php echo $bingo_card_type !== '1-75' ? 'display: none;' : ''; ?>">
                                    <span class="lbcg-card-subtitle-text"><?php
                                        echo $additional_spec_part;
                                        echo ! empty( $bingo_card_spec_title ) ? '<span>' . implode( '</span><span>', $bingo_card_spec_title ) . '</span>' : '';
                                        echo $additional_spec_part; ?></span>
                                </div>
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
										for ( $i = 1; $i <= $grid_sq_count; $i ++ ):
											if ( (int) ceil( $grid_sq_count / 2 ) === $i && $bingo_grid_free_square ) {
												$is_free_space = true;
											} else {
												$is_free_space = false;
											} ?>
                                            <div class="lbcg-card-col<?php echo $is_free_space ? ' lbcg-free-space' : ''; ?>">
                                                <span class="lbcg-card-text"><?php
                                                    if ( $is_free_space ) {
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
    </main>
</div>
<?php if ( get_post_type() === 'bingo_theme' ):
	ob_start();
	wp_editor( isset( $data['bt_intro_text'] ) ? $data['bt_intro_text'][0] : '', 'intro-text', [
		'wpautop'       => false,
		'media_buttons' => false,
		'textarea_name' => 'bt_intro_text',
		'textarea_rows' => 10
	] );
	$editor = ob_get_clean();
	?>
    <div class="lbcg-wp-editor">
        <label for="intro-text">Intro text</label><?php echo $editor; ?>
    </div>
<?php endif; ?>
