<?php
$post_id = get_the_ID();
$bingo_card_type = get_post_meta($post_id, 'bingo_card_type', true);
$bingo_grid_size = get_post_meta($post_id, 'bingo_grid_size', true);

$special_types = array('1-9', '1-75', '1-90');
?>
<table>
    <tbody>
        <tr>
            <td>
                <label for="bingo-card-type">Select bingo card type:</label>
            </td>
            <td>
                <select id="bingo-card-type" name="bingo_card_type">
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
                <label for="bingo-grid-size">Grid size:</label>
            </td>
            <td>
                <select id="bingo-grid-size" <?php echo in_array($bingo_card_type, $special_types) ? 'disabled' : ''; ?>>
                    <option value="3x3" <?php echo $bingo_grid_size === '3x3' ? 'selected="selected"' : ''; ?>>3x3</option>
                    <option value="4x4" <?php echo $bingo_grid_size === '4x4' ? 'selected="selected"' : ''; ?>>4x4</option>
                    <option value="5x5" <?php echo $bingo_grid_size === '5x5' ? 'selected="selected"' : ''; ?>>5x5</option>
                    <option value="9x3" <?php echo $bingo_grid_size === '9x3' ? 'selected="selected"' : ''; ?> disabled>9x3 (special)</option>
                </select>
            </td>
            <input type="hidden" name="bingo_grid_size" value="<?php echo $bingo_grid_size; ?>"/>
        </tr>
    </tbody>
</table>