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

$special_types = array('1-9', '1-75', '1-90');
?>
<table>
    <tbody>
        <tr>
            <td>
                <label for="bc-type">Select bingo card type:</label>
            </td>
            <td>
                <select id="bc-type" name="bingo_card_type">
                    <option value="1-9" <?php echo $bingo_card_type === '1-9' ? 'selected="selected"' : ''; ?>>1-9</option>
                    <option value="1-25" <?php echo $bingo_card_type === '1-25' ? 'selected="selected"' : ''; ?>>1-25</option>
                    <option value="1-75" <?php echo $bingo_card_type === '1-75' ? 'selected="selected"' : ''; ?>>1-75</option>
                    <option value="1-80" <?php echo $bingo_card_type === '1-80' ? 'selected="selected"' : ''; ?>>1-80</option>
                    <option value="1-90" <?php echo $bingo_card_type === '1-90' ? 'selected="selected"' : ''; ?>>1-90</option>
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
                <textarea id="bc-title" name="bingo_card_title"><?php echo !empty($bingo_card_title) ? $bingo_card_title : 'B I N G O'; ?></textarea>
            </td>
            <td class="bc-content" <?php echo $bingo_card_type === '1-75' || $bingo_card_type === '1-90' ? 'style="display: none;"' : ''; ?>>
                <label for="bc-content">Enter words/emojis or numbers:</label>
                <p>Note: Please fill <span id="content-items-count"><?php echo $words_count; ?></span> words/emojis or numbers, each in new line.</p>
            </td>
            <td class="bc-content" <?php echo $bingo_card_type === '1-75' || $bingo_card_type === '1-90' ? 'style="display: none;"' : ''; ?>>
                <textarea id="bc-content" name="bingo_card_content"><?php echo $bingo_card_content; ?></textarea>
            </td>
            <td class="bc-title-1-75" <?php echo $bingo_card_type !== '1-75' ? 'style="display: none;"' : ''; ?>>
                <label>Bingo card title for 1-75:</label>
            </td>
            <td class="bc-title-1-75" <?php echo $bingo_card_type !== '1-75' ? 'style="display: none;"' : ''; ?>>
                <input type="text" name="bingo_card_spec_title[]" class="letter-title" size="1" maxlength="1" value="<?php echo $bingo_card_spec_title[0]; ?>">
                <input type="text" name="bingo_card_spec_title[]" class="letter-title" size="1" maxlength="1" value="<?php echo $bingo_card_spec_title[1]; ?>">
                <input type="text" name="bingo_card_spec_title[]" class="letter-title" size="1" maxlength="1" value="<?php echo $bingo_card_spec_title[2]; ?>">
                <input type="text" name="bingo_card_spec_title[]" class="letter-title" size="1" maxlength="1" value="<?php echo $bingo_card_spec_title[3]; ?>">
                <input type="text" name="bingo_card_spec_title[]" class="letter-title" size="1" maxlength="1" value="<?php echo $bingo_card_spec_title[4]; ?>">
            </td>
        </tr>
        <tr class="white-space">&nbsp;</tr>
        <tr>
            <td rowspan="2">
                <label>Header background color:</label>
            </td>
            <td rowspan="2">
                <input type="color" name="bc_header[color]" value="<?php echo $bc_header['color']; ?>">
            </td>
            <td>
                <label>Header background image:</label>
            </td>
            <td>
                <?php if (!empty($bc_header['image'])): ?>
                <img src="<?php echo wp_get_attachment_image_url($bc_header['image']); ?>" style="width: 90px;">
                <?php endif; ?>
                <input type="file" accept="image/*" name="bc_header[image]">
            </td>
        </tr>
        <tr>
            <td>
                <label for="bci-header-repeat">Image repeat:</label>
                <input type="checkbox" id="bci-header-repeat" name="bc_header[repeat]" <?php echo $bc_header['repeat'] === 'on' ? 'checked' : ''; ?>>
            </td>
            <td>
                <label for="bci-header-opacity">Image opacity (%):</label>
                <input type="number" id="bci-header-opacity" name="bc_header[opacity]" min="0" max="100" value="<?php echo $bc_header['opacity']; ?>">
            </td>
        </tr>
        <tr>
            <td rowspan="2">
                <label>Grid background color:</label>
            </td>
            <td rowspan="2">
                <input type="color" name="bc_grid[color]" value="<?php echo $bc_grid['color']; ?>">
            </td>
            <td>
                <label>Grid background image:</label>
            </td>
            <td>
                <?php if (!empty($bc_grid['image'])): ?>
                    <img src="<?php echo wp_get_attachment_image_url($bc_grid['image']); ?>" style="width: 90px;">
                <?php endif; ?>
                <input type="file" accept="image/*" name="bc_grid[image]">
            </td>
        </tr>
        <tr>
            <td>
                <label for="bci-grid-repeat">Image repeat:</label>
                <input type="checkbox" id="bci-grid-repeat" name="bc_grid[repeat]" <?php echo $bc_grid['repeat'] === 'on' ? 'checked' : ''; ?>>
            </td>
            <td>
                <label for="bci-grid-opacity">Image opacity (%):</label>
                <input type="number" id="bci-grid-opacity" name="bc_grid[opacity]" min="0" max="100" value="<?php echo $bc_grid['opacity']; ?>">
            </td>
        </tr>
        <tr>
            <td rowspan="2">
                <label>Card background color:</label>
            </td>
            <td rowspan="2">
                <input type="color" name="bc_card[color]" value="<?php echo $bc_card['color']; ?>">
            </td>
            <td>
                <label>Card background image:</label>
            </td>
            <td>
                <?php if (!empty($bc_card['image'])): ?>
                    <img src="<?php echo wp_get_attachment_image_url($bc_card['image']); ?>" style="width: 90px;">
                <?php endif; ?>
                <input type="file" accept="image/*" name="bc_card[image]">
            </td>
        </tr>
        <tr>
            <td>
                <label for="bci-card-repeat">Image repeat:</label>
                <input type="checkbox" id="bci-card-repeat" name="bc_card[repeat]" <?php echo $bc_card['repeat'] === 'on' ? 'checked' : ''; ?>>
            </td>
            <td>
                <label for="bci-card-opacity">Image opacity (%):</label>
                <input type="number" id="bci-card-opacity" name="bc_card[opacity]" min="0" max="100" value="<?php echo $bc_card['opacity']; ?>">
            </td>
        </tr>
        <tr class="white-space">&nbsp;</tr>
        <tr>
            <td>
                <label for="bc-font">Font:</label>
            </td>
            <td>
                <select id="bc-font" name="bingo_card_font">
                    <option value="mochiy-pop-p-one" <?php echo !empty($data['bingo_card_font'][0]) && $data['bingo_card_font'][0] === 'mochiy-pop-p-one' ? 'selected="selected"' : ''; ?>>Mochiy Pop P One</option>
                    <option value="dancing-script" <?php echo !empty($data['bingo_card_font'][0]) && $data['bingo_card_font'][0] === 'dancing-script' ? 'selected="selected"' : ''; ?>>Dancing Script</option>
                    <option value="saira-condensed" <?php echo !empty($data['bingo_card_font'][0]) && $data['bingo_card_font'][0] === 'saira-condensed' ? 'selected="selected"' : ''; ?>>Saira Condensed</option>
                    <option value="righteous" <?php echo !empty($data['bingo_card_font'][0]) && $data['bingo_card_font'][0] === 'righteous' ? 'selected="selected"' : ''; ?>>Righteous</option>
                </select>
            </td>
            <td id="free-square">
                <label for="bc-free-square">Free square:</label>
                <input type="checkbox" id="bc-free-square" name="bingo_card_free_square" <?php echo !empty($data['bingo_card_free_square'][0]) && $data['bingo_card_free_square'][0] === 'on' ? 'checked' : ''; ?>>
            </td>
            <td>&nbsp;</td>
        </tr>
        <tr class="white-space">&nbsp;</tr>
        <tr>
            <td>
                <label for="bc-custom-css">Custom CSS:</label>
            </td>
            <td>
                <textarea id="bc-custom-css" name="bingo_card_custom_css"><?php echo !empty($data['bingo_card_custom_css'][0]) ? $data['bingo_card_custom_css'][0] : ''; ?></textarea>
            </td>
        </tr>
    </tbody>
</table>
<input type="hidden" name="bingo_grid_size" value="<?php echo $bingo_grid_size; ?>"/>