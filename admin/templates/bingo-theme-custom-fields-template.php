<?php
global $post;
$data = get_post_meta($post->ID);
$bingo_card_type = !empty($data['bingo_card_type'][0]) ? $data['bingo_card_type'][0] : '1-9';
$bingo_grid_size = !empty($data['bingo_grid_size'][0]) ? $data['bingo_grid_size'][0] : '3x3';
$bingo_card_title = !empty($data['bingo_card_title'][0]) ? $data['bingo_card_title'][0] : '';
if (!empty($data['bingo_card_spec_title'][0])) {
    $bingo_card_spec_title = explode('|', $data['bingo_card_spec_title'][0]);
} else {
    $bingo_card_spec_title = ['B', 'I', 'N', 'G', 'O'];
}
$result = LBCGHelper::get_bg_default_content($bingo_card_type, $bingo_grid_size);
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
        'opacity' => 100,
        'repeat' => 'no-repeat',
        'bg_pos' => 'center center',
        'bg_size' => 'cover'
    ];
}
if (!empty($data['bc_grid'][0])) {
    $bc_grid = unserialize($data['bc_grid'][0]);
} else {
    $bc_grid = [
        'color' => '#997d3c',
        'image' => '',
        'opacity' => 100,
        'repeat' => 'no-repeat',
        'bg_pos' => 'center center',
        'bg_size' => 'cover'
    ];
}
if (!empty($data['bc_card'][0])) {
    $bc_card = unserialize($data['bc_card'][0]);
} else {
    $bc_card = [
        'color' => '#d6be89',
        'image' => '',
        'opacity' => 100,
        'repeat' => 'no-repeat',
        'bg_pos' => 'center center',
        'bg_size' => 'cover'
    ];
}

$special_types = array('1-9', '1-75', '1-90');
?>
<input type="hidden" name="lbcg_action" value="save_bc_post">
<table>
    <tbody>
    <tr>
        <td>
            <label for="bc-type">Select bingo card type:</label>
        </td>
        <td>
            <select id="bc-type" name="bingo_card_type">
                <option value="1-9" <?php echo $bingo_card_type === '1-9' ? 'selected="selected"' : ''; ?>>1-9</option>
                <option value="1-25" <?php echo $bingo_card_type === '1-25' ? 'selected="selected"' : ''; ?>>1-25
                </option>
                <option value="1-75" <?php echo $bingo_card_type === '1-75' ? 'selected="selected"' : ''; ?>>1-75
                </option>
                <option value="1-80" <?php echo $bingo_card_type === '1-80' ? 'selected="selected"' : ''; ?>>1-80
                </option>
                <option value="1-90" <?php echo $bingo_card_type === '1-90' ? 'selected="selected"' : ''; ?>>1-90
                </option>
                <option value="1-100" <?php echo $bingo_card_type === '1-100' ? 'selected="selected"' : ''; ?>>1-100
                </option>
            </select>
        </td>
        <td>
            <label for="bc-size">Grid size:</label>
        </td>
        <td>
            <select id="bc-size" <?php echo in_array($bingo_card_type, $special_types) ? 'disabled' : ''; ?>>
                <option value="3x3" <?php echo $bingo_grid_size === '3x3' ? 'selected="selected"' : ''; ?>>3x3</option>
                <option value="4x4" <?php echo $bingo_grid_size === '4x4' ? 'selected="selected"' : ''; ?>>4x4</option>
                <option value="5x5" <?php echo $bingo_grid_size === '5x5' ? 'selected="selected"' : ''; ?>>5x5</option>
                <option value="9x3" <?php echo $bingo_grid_size === '9x3' ? 'selected="selected"' : ''; ?> disabled>9x3 (special)</option>
            </select>
        </td>
    </tr>
    <tr class="white-space">&nbsp;</tr>
    <tr>
        <td>
            <label for="bc-title">Bingo card title:</label>
        </td>
        <td>
            <input type="text" id="bc-title" name="bingo_card_title" value="<?php echo !empty($bingo_card_title) ? $bingo_card_title : 'B I N G O'; ?>">
        </td>
        <td class="bc-content" <?php echo $bingo_card_type === '1-75' || $bingo_card_type === '1-90' ? 'style="display: none;"' : ''; ?>>
            <label for="bc-content">Enter words/emojis or numbers:</label>
            <p>Note: Please fill <span id="content-items-count"><?php echo $words_count; ?></span> words/emojis or
                numbers, each in new line.</p>
        </td>
        <td class="bc-content" <?php echo $bingo_card_type === '1-75' || $bingo_card_type === '1-90' ? 'style="display: none;"' : ''; ?>>
            <textarea id="bc-content" name="bingo_card_content"><?php echo $bingo_card_content; ?></textarea>
        </td>
        <td class="bc-title-1-75" <?php echo $bingo_card_type !== '1-75' ? 'style="display: none;"' : ''; ?>>
            <label>Bingo card title for 1-75:</label>
        </td>
        <td class="bc-title-1-75" <?php echo $bingo_card_type !== '1-75' ? 'style="display: none;"' : ''; ?>>
            <input type="text" name="bingo_card_spec_title[0]" class="letter-title" size="1" maxlength="1"
                   value="<?php echo $bingo_card_spec_title[0]; ?>">
            <input type="text" name="bingo_card_spec_title[1]" class="letter-title" size="1" maxlength="1"
                   value="<?php echo $bingo_card_spec_title[1]; ?>">
            <input type="text" name="bingo_card_spec_title[2]" class="letter-title" size="1" maxlength="1"
                   value="<?php echo $bingo_card_spec_title[2]; ?>">
            <input type="text" name="bingo_card_spec_title[3]" class="letter-title" size="1" maxlength="1"
                   value="<?php echo $bingo_card_spec_title[3]; ?>">
            <input type="text" name="bingo_card_spec_title[4]" class="letter-title" size="1" maxlength="1"
                   value="<?php echo $bingo_card_spec_title[4]; ?>">
        </td>
    </tr>
    <tr class="white-space">&nbsp;</tr>
    <tr>
        <td>
            <label class="bca-subtitle">Header</label>
        </td>
    </tr>
    <tr>
        <td rowspan="2">
            <label>Header background color:</label>
            <input type="color" name="bc_header[color]" value="<?php echo $bc_header['color']; ?>">
        </td>
        <td rowspan="2">
            <?php $image = wp_get_attachment_image_url($bc_header['image']); ?>
            <a href="#" class="bc-image-upload button button-primary button-large"><?php
                echo $image ? '<img src="' . $image . '" class="lbcg-image-uploaded"/>' : 'Upload image';
                ?></a>
            <a href="#" class="bc-remove-uploaded-image button button-primary button-large" <?php
            echo $image === false ? 'style="display: none;"' : ''; ?>>Remove image</a>
            <input type="hidden" name="bc_header[image]"
                   value="<?php echo $image !== false ? $bc_header['image'] : 0; ?>">
        </td>
        <td>
            <label for="bci-header-opacity">Image opacity (%):</label>
            <input type="number" id="bci-header-opacity" name="bc_header[opacity]" min="0" max="100"
                   placeholder="0-100" value="<?php echo $bc_header['opacity']; ?>">
        </td>
        <td>
            <select name="bc_header[repeat]">
                <option value="no-repeat" <?php echo $bc_header['repeat'] === 'no-repeat' ? 'selected' : ''; ?>>Background Repeat</option>
                <option value="repeat" <?php echo $bc_header['repeat'] === 'repeat' ? 'selected' : ''; ?>>Repeat</option>
                <option value="repeat-x" <?php echo $bc_header['repeat'] === 'repeat-x' ? 'selected' : ''; ?>>Repeat X</option>
                <option value="repeat-y" <?php echo $bc_header['repeat'] === 'repeat-y' ? 'selected' : ''; ?>>Repeat Y</option>
                <option value="no-repeat" <?php echo $bc_header['repeat'] === 'no-repeat' ? 'selected' : ''; ?>>No Repeat</option>
            </select>
        </td>
    </tr>
    <tr>
        <td>
            <select name="bc_header[bg_pos]">
                <option value="0 0" <?php echo !empty($bc_header['bg_pos']) && $bc_header['bg_pos'] === '0 0' ? 'selected' : ''; ?>>Background Position</option>
                <option value="top left" <?php echo !empty($bc_header['bg_pos']) && $bc_header['bg_pos'] === 'top left' ? 'selected' : ''; ?>>Top Left</option>
                <option value="top center" <?php echo !empty($bc_header['bg_pos']) && $bc_header['bg_pos'] === 'top center' ? 'selected' : ''; ?>>Top Center</option>
                <option value="top right" <?php echo !empty($bc_header['bg_pos']) && $bc_header['bg_pos'] === 'top right' ? 'selected' : ''; ?>>Top Right</option>
                <option value="center left" <?php echo !empty($bc_header['bg_pos']) && $bc_header['bg_pos'] === 'center left' ? 'selected' : ''; ?>>Center Left</option>
                <option value="center center" <?php echo !empty($bc_header['bg_pos']) && $bc_header['bg_pos'] === 'center center' ? 'selected' : ''; ?>>Center Center</option>
                <option value="center right" <?php echo !empty($bc_header['bg_pos']) && $bc_header['bg_pos'] === 'center right' ? 'selected' : ''; ?>>Center Right</option>
                <option value="bottom left" <?php echo !empty($bc_header['bg_pos']) && $bc_header['bg_pos'] === 'bottom left' ? 'selected' : ''; ?>>Bottom Left</option>
                <option value="bottom center" <?php echo !empty($bc_header['bg_pos']) && $bc_header['bg_pos'] === 'bottom center' ? 'selected' : ''; ?>>Bottom Center</option>
                <option value="bottom right" <?php echo !empty($bc_header['bg_pos']) && $bc_header['bg_pos'] === 'bottom right' ? 'selected' : ''; ?>>Bottom Right</option>
            </select>
        </td>
        <td>
            <select name="bc_header[bg_size]" class="bc-size lbcg-select" data-bct="header">
                <option value="auto" <?php echo !empty($bc_header['bg_size']) && $bc_header['bg_size'] === 'auto' ? 'selected' : ''; ?>>Background Size</option>
                <option value="auto" <?php echo !empty($bc_header['bg_size']) && $bc_header['bg_size'] === 'auto' ? 'selected' : ''; ?>>Auto</option>
                <option value="contain" <?php echo !empty($bc_header['bg_size']) && $bc_header['bg_size'] === 'contain' ? 'selected' : ''; ?>>Contain</option>
                <option value="cover" <?php echo !empty($bc_header['bg_size']) && $bc_header['bg_size'] === 'cover' ? 'selected' : ''; ?>>Cover</option>
            </select>
        </td>
    </tr>
    <tr class="white-space">&nbsp;</tr>
    <tr>
        <td>
            <label class="bca-subtitle">Grid</label>
        </td>
    </tr>
    <tr>
        <td rowspan="2">
            <label>Grid background color:</label>
            <input type="color" name="bc_grid[color]" value="<?php echo $bc_grid['color']; ?>">
        </td>
        <td rowspan="2">
            <?php $image = wp_get_attachment_image_url($bc_grid['image']); ?>
            <a href="#" class="bc-image-upload button button-primary button-large"><?php
                echo $image ? '<img src="' . $image . '" class="lbcg-image-uploaded"/>' : 'Upload image';
                ?></a>
            <a href="#" class="bc-remove-uploaded-image button button-primary button-large" <?php
            echo $image === false ? 'style="display: none;"' : ''; ?>>Remove image</a>
            <input type="hidden" name="bc_grid[image]" value="<?php echo $image !== false ? $bc_grid['image'] : 0; ?>">
        </td>
        <td>
            <label for="bci-grid-opacity">Image opacity (%):</label>
            <input type="number" id="bci-grid-opacity" name="bc_grid[opacity]" min="0" max="100"
                   placeholder="0-100" value="<?php echo $bc_grid['opacity']; ?>">
        </td>
        <td>
            <select name="bc_grid[repeat]">
                <option value="no-repeat" <?php echo $bc_grid['repeat'] === 'no-repeat' ? 'selected' : ''; ?>>Background Repeat</option>
                <option value="repeat" <?php echo $bc_grid['repeat'] === 'repeat' ? 'selected' : ''; ?>>Repeat</option>
                <option value="repeat-x" <?php echo $bc_grid['repeat'] === 'repeat-x' ? 'selected' : ''; ?>>Repeat X</option>
                <option value="repeat-y" <?php echo $bc_grid['repeat'] === 'repeat-y' ? 'selected' : ''; ?>>Repeat Y</option>
                <option value="no-repeat" <?php echo $bc_grid['repeat'] === 'no-repeat' ? 'selected' : ''; ?>>No Repeat</option>
            </select>
        </td>
    </tr>
    <tr>
        <td>
            <select name="bc_grid[bg_pos]">
                <option value="0 0" <?php echo !empty($bc_grid['bg_pos']) && $bc_grid['bg_pos'] === '0 0' ? 'selected' : ''; ?>>Background Position</option>
                <option value="top left" <?php echo !empty($bc_grid['bg_pos']) && $bc_grid['bg_pos'] === 'top left' ? 'selected' : ''; ?>>Top Left</option>
                <option value="top center" <?php echo !empty($bc_grid['bg_pos']) && $bc_grid['bg_pos'] === 'top center' ? 'selected' : ''; ?>>Top Center</option>
                <option value="top right" <?php echo !empty($bc_grid['bg_pos']) && $bc_grid['bg_pos'] === 'top right' ? 'selected' : ''; ?>>Top Right</option>
                <option value="center left" <?php echo !empty($bc_grid['bg_pos']) && $bc_grid['bg_pos'] === 'center left' ? 'selected' : ''; ?>>Center Left</option>
                <option value="center center" <?php echo !empty($bc_grid['bg_pos']) && $bc_grid['bg_pos'] === 'center center' ? 'selected' : ''; ?>>Center Center</option>
                <option value="center right" <?php echo !empty($bc_grid['bg_pos']) && $bc_grid['bg_pos'] === 'center right' ? 'selected' : ''; ?>>Center Right</option>
                <option value="bottom left" <?php echo !empty($bc_grid['bg_pos']) && $bc_grid['bg_pos'] === 'bottom left' ? 'selected' : ''; ?>>Bottom Left</option>
                <option value="bottom center" <?php echo !empty($bc_grid['bg_pos']) && $bc_grid['bg_pos'] === 'bottom center' ? 'selected' : ''; ?>>Bottom Center</option>
                <option value="bottom right" <?php echo !empty($bc_grid['bg_pos']) && $bc_grid['bg_pos'] === 'bottom right' ? 'selected' : ''; ?>>Bottom Right</option>
            </select>
        </td>
        <td>
            <select name="bc_grid[bg_size]">
                <option value="auto" <?php echo !empty($bc_grid['bg_size']) && $bc_grid['bg_size'] === 'auto' ? 'selected' : ''; ?>>Background Size</option>
                <option value="auto" <?php echo !empty($bc_grid['bg_size']) && $bc_grid['bg_size'] === 'auto' ? 'selected' : ''; ?>>Auto</option>
                <option value="contain" <?php echo !empty($bc_grid['bg_size']) && $bc_grid['bg_size'] === 'contain' ? 'selected' : ''; ?>>Contain</option>
                <option value="cover" <?php echo !empty($bc_grid['bg_size']) && $bc_grid['bg_size'] === 'cover' ? 'selected' : ''; ?>>Cover</option>
            </select>
        </td>
    </tr>
    <tr class="white-space">&nbsp;</tr>
    <tr>
        <td>
            <label class="bca-subtitle">Card</label>
        </td>
    </tr>
    <tr>
        <td rowspan="2">
            <label>Card background color:</label>
            <input type="color" name="bc_card[color]" value="<?php echo $bc_card['color']; ?>">
        </td>
        <td rowspan="2">
            <?php $image = wp_get_attachment_image_url($bc_card['image']); ?>
            <a href="#" class="bc-image-upload button button-primary button-large"><?php
                echo $image ? '<img src="' . $image . '" class="lbcg-image-uploaded"/>' : 'Upload image';
                ?></a>
            <a href="#" class="bc-remove-uploaded-image button button-primary button-large" <?php
            echo $image === false ? 'style="display: none;"' : ''; ?>>Remove image</a>
            <input type="hidden" name="bc_card[image]" value="<?php echo $image !== false ? $bc_card['image'] : 0; ?>">
        </td>
        <td>
            <label for="bci-card-opacity">Image opacity (%):</label>
            <input type="number" id="bci-card-opacity" name="bc_card[opacity]" min="0" max="100"
                   placeholder="0-100" value="<?php echo $bc_card['opacity']; ?>">
        </td>
        <td>
            <select name="bc_card[repeat]">
                <option value="no-repeat" <?php echo $bc_card['repeat'] === 'no-repeat' ? 'selected' : ''; ?>>Background Repeat</option>
                <option value="repeat" <?php echo $bc_card['repeat'] === 'repeat' ? 'selected' : ''; ?>>Repeat</option>
                <option value="repeat-x" <?php echo $bc_card['repeat'] === 'repeat-x' ? 'selected' : ''; ?>>Repeat X</option>
                <option value="repeat-y" <?php echo $bc_card['repeat'] === 'repeat-y' ? 'selected' : ''; ?>>Repeat Y</option>
                <option value="no-repeat" <?php echo $bc_card['repeat'] === 'no-repeat' ? 'selected' : ''; ?>>No Repeat</option>
            </select>
        </td>
    </tr>
    <tr>
        <td>
            <select name="bc_card[bg_pos]">
                <option value="0 0" <?php echo !empty($bc_card['bg_pos']) && $bc_card['bg_pos'] === '0 0' ? 'selected' : ''; ?>>Background Position</option>
                <option value="top left" <?php echo !empty($bc_card['bg_pos']) && $bc_card['bg_pos'] === 'top left' ? 'selected' : ''; ?>>Top Left</option>
                <option value="top center" <?php echo !empty($bc_card['bg_pos']) && $bc_card['bg_pos'] === 'top center' ? 'selected' : ''; ?>>Top Center</option>
                <option value="top right" <?php echo !empty($bc_card['bg_pos']) && $bc_card['bg_pos'] === 'top right' ? 'selected' : ''; ?>>Top Right</option>
                <option value="center left" <?php echo !empty($bc_card['bg_pos']) && $bc_card['bg_pos'] === 'center left' ? 'selected' : ''; ?>>Center Left</option>
                <option value="center center" <?php echo !empty($bc_card['bg_pos']) && $bc_card['bg_pos'] === 'center center' ? 'selected' : ''; ?>>Center Center</option>
                <option value="center right" <?php echo !empty($bc_card['bg_pos']) && $bc_card['bg_pos'] === 'center right' ? 'selected' : ''; ?>>Center Right</option>
                <option value="bottom left" <?php echo !empty($bc_card['bg_pos']) && $bc_card['bg_pos'] === 'bottom left' ? 'selected' : ''; ?>>Bottom Left</option>
                <option value="bottom center" <?php echo !empty($bc_card['bg_pos']) && $bc_card['bg_pos'] === 'bottom center' ? 'selected' : ''; ?>>Bottom Center</option>
                <option value="bottom right" <?php echo !empty($bc_card['bg_pos']) && $bc_card['bg_pos'] === 'bottom right' ? 'selected' : ''; ?>>Bottom Right</option>
            </select>
        </td>
        <td>
            <select name="bc_card[bg_size]">
                <option value="auto" <?php echo !empty($bc_card['bg_size']) && $bc_card['bg_size'] === 'auto' ? 'selected' : ''; ?>>Background Size</option>
                <option value="auto" <?php echo !empty($bc_card['bg_size']) && $bc_card['bg_size'] === 'auto' ? 'selected' : ''; ?>>Auto</option>
                <option value="contain" <?php echo !empty($bc_card['bg_size']) && $bc_card['bg_size'] === 'contain' ? 'selected' : ''; ?>>Contain</option>
                <option value="cover" <?php echo !empty($bc_card['bg_size']) && $bc_card['bg_size'] === 'cover' ? 'selected' : ''; ?>>Cover</option>
            </select>
        </td>
    </tr>
    <tr class="white-space">&nbsp;</tr>
    <tr>
        <td>
            <label for="bc-font">Font:</label>
        </td>
        <td>
            <select id="bc-font" name="bingo_card_font">
                <?php foreach (LBCGHelper::$fonts as $key => $font): ?>
                    <option value="<?php echo $key; ?>" <?php echo !empty($data['bingo_card_font'][0]) && $data['bingo_card_font'][0] === $key ? 'selected="selected"' : ''; ?>><?php
                        echo $font['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td id="free-square" <?php echo $bingo_card_type === '1-75' || $bingo_card_type === '1-90' || $bingo_grid_size === '4x4' ? 'style="display: none;"' : ''; ?>>
            <label for="bc-free-square">Free square:</label>
            <input type="checkbox" id="bc-free-square"
                   name="bingo_card_free_square" <?php echo !empty($data['bingo_card_free_square'][0]) && $data['bingo_card_free_square'][0] === 'on' || $bingo_card_type === '1-75' ? 'checked' : ''; ?>>
        </td>
        <td>&nbsp;</td>
    </tr>
    <tr class="white-space">&nbsp;</tr>
    <tr>
        <td>
            <label for="bc-custom-css">Custom CSS:</label>
        </td>
        <td>
            <textarea id="bc-custom-css"
                      name="bingo_card_custom_css"><?php echo !empty($data['bingo_card_custom_css'][0]) ? $data['bingo_card_custom_css'][0] : ''; ?></textarea>
        </td>
    </tr>
    </tbody>
</table>
<input type="hidden" name="bingo_grid_size" value="<?php echo $bingo_grid_size; ?>"/>