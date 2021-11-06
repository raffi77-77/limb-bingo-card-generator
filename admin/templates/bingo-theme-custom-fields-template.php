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
$bingo_card_content = !empty($data['bingo_card_content'][0]) ? $data['bingo_card_content'][0] : BingoCardHelper::get_bg_default_content($bingo_card_type, $bingo_card_type);

$special_types = array('1-9', '1-75', '1-90');
?>
<table>
    <tbody>
        <tr>
            <td>
                <label for="bg-type">Select bingo card type:</label>
            </td>
            <td>
                <select id="bg-type" name="bingo_card_type">
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
                <label for="bg-size">Grid size:</label>
            </td>
            <td>
                <select id="bg-size" <?php echo in_array($bingo_card_type, $special_types) ? 'disabled' : ''; ?>>
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
                <label for="bg-title">Bingo card title:</label>
            </td>
            <td>
                <textarea id="bg-title" name="bingo_card_title"><?php echo !empty($bingo_card_title) ? $bingo_card_title : 'B I N G O'; ?></textarea>
            </td>
            <td class="bg-title-1-75" <?php echo $bingo_card_type !== '1-75' ? 'style="display: none;"' : ''; ?>>
                <label>Bingo card title for 1-75:</label>
            </td>
            <td class="bg-title-1-75" <?php echo $bingo_card_type !== '1-75' ? 'style="display: none;"' : ''; ?>>
                <input type="text" name="bingo_card_spec_title[]" class="letter-title" size="1" maxlength="1" value="<?php echo $bingo_card_spec_title[0]; ?>">
                <input type="text" name="bingo_card_spec_title[]" class="letter-title" size="1" maxlength="1" value="<?php echo $bingo_card_spec_title[1]; ?>">
                <input type="text" name="bingo_card_spec_title[]" class="letter-title" size="1" maxlength="1" value="<?php echo $bingo_card_spec_title[2]; ?>">
                <input type="text" name="bingo_card_spec_title[]" class="letter-title" size="1" maxlength="1" value="<?php echo $bingo_card_spec_title[3]; ?>">
                <input type="text" name="bingo_card_spec_title[]" class="letter-title" size="1" maxlength="1" value="<?php echo $bingo_card_spec_title[4]; ?>">
            </td>
        </tr>
        <tr>
            <td>
                <label for="bg-content">Enter words/emojis or numbers:</label>
            </td>
            <td>
                <textarea id="bg-content" name="bingo_card_content"><?php echo $bingo_card_content; ?></textarea>
            </td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>
<input type="hidden" name="bingo_grid_size" value="<?php echo $bingo_grid_size; ?>"/>