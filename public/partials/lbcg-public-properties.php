<style type="text/css">
    :root {
        /* card styles */

        --lbcg-card-bg-image: <?php echo !empty($bc_card['image']) ? 'url(' . wp_get_attachment_image_url($bc_card['image'], 'large') . ')' : 'none'; ?>;
        --lbcg-card-bg-pos: <?php echo !empty($bc_card['bg_pos']) ? $bc_card['bg_pos'] : '0 0'; ?>;
        --lbcg-card-bg-repeat: <?php echo !empty($bc_card['repeat'])  ? $bc_card['repeat'] : 'no-repeat'; ?>;
        --lbcg-card-bg-size: <?php echo !empty($bc_card['bg_size']) ? $bc_card['bg_size'] : 'auto'; ?>;
        --lbcg-card-bg-color: <?php echo !empty($bc_card['color']) ? $bc_card['color'] : '#FFF'; ?>;
        --lbcg-card-bg-opacity: <?php echo isset($bc_card['opacity']) ? $bc_card['opacity'] / 100 : 1; ?>;

        /* header styles */

        --lbcg-header-font-size: 16px;
        --lbcg-header-height: 48px;
        --lbcg-header-font-family: '<?php echo !empty($data['bingo_card_font'][0]) ? LBCG_Helper::$fonts[$data['bingo_card_font'][0]]['name'] : LBCG_Helper::$fonts['mochiy-pop-p-one']['name']; ?>', sans-serif;
        --lbcg-header-text-color: <?php echo !empty($bc_header['font_color']) ? $bc_header['font_color'] : '#ffffff'; ?>;
        --lbcg-header-bg-image: <?php echo !empty($bc_header['image']) ? 'url(' . wp_get_attachment_image_url($bc_header['image'], 'large') . ')' : 'none' ?>;
        --lbcg-header-bg-pos: <?php echo !empty($bc_header['bg_pos']) ? $bc_header['bg_pos'] : '0 0'; ?>;
        --lbcg-header-bg-repeat: <?php echo !empty($bc_header['repeat']) ? $bc_header['repeat'] : 'no-repeat'; ?>;
        --lbcg-header-bg-size: <?php echo !empty($bc_header['bg_size']) ? $bc_header['bg_size'] : 'auto'; ?>;
        --lbcg-header-bg-color: <?php echo !empty($bc_header['color']) ? $bc_header['color'] : 'transparent'; ?>;
        --lbcg-header-bg-opacity: <?php echo isset($bc_header['opacity']) ? $bc_header['opacity'] / 100 : 1; ?>;

        /* body styles */

        --lbcg-grid-font-size: <?php LBCG_Helper::$font_size = ! empty( $data['lbcg_font_size'][0] ) ? (int) $data['lbcg_font_size'][0] : (!empty($data['bingo_card_type'][0]) ? ($data['bingo_card_type'][0] === '1-90' ?
        16 : ($data['bingo_card_type'][0]
        === '1-75' ? 31.5 : 16)) :
        16); echo LBCG_Helper::$font_size . 'px'; ?>;
        --lbcg-grid-font-family: '<?php echo !empty($data['bingo_card_font'][0]) ? LBCG_Helper::$fonts[$data['bingo_card_font'][0]]['name'] : LBCG_Helper::$fonts['mochiy-pop-p-one']['name']; ?>', sans-serif;
        --lbcg-grid-line-height: <?php
        if (!empty($data['bingo_card_wrap_words'][0]) && $data['bingo_card_wrap_words'][0] === 'on') {
            echo 1;
        } else if (!empty($data['bingo_card_type'][0])) {
            if ($data['bingo_card_type'][0] === '1-90') {
                echo '33.3px';
            } elseif ($data['bingo_grid_size'][0] === '3x3') {
                echo '102px';
            } elseif ($data['bingo_grid_size'][0] === '4x4') {
                echo '76.25px';
                } else {
                    echo '60.8px';
                }
        } else {
            echo '102px';
        } ?>;
        /*--lbcg-grid-wrap-words:
    <?php echo !empty($data['bingo_card_wrap_words'][0]) && $data['bingo_card_wrap_words'][0] === 'on' ? 'break-word' : 'anywhere'; ?> ;*/
        --lbcg-grid-text-color: <?php echo !empty($bc_grid['font_color']) ? $bc_grid['font_color'] : '#000'; ?>;
        --lbcg-grid-border-color: <?php echo !empty($bc_grid['border_color']) ? $bc_grid['border_color'] : '#000'; ?>;
        --lbcg-grid-square-text-color: <?php echo !empty($grid_square['font_color']) ? $grid_square['font_color'] : '#ffffff'; ?>;
        --lbcg-grid-square-bg-color: <?php echo !empty($grid_square['color']) ? $grid_square['color'] : '#000'; ?>;
        --lbcg-grid-bg-image: <?php echo !empty($bc_grid['image']) ? 'url(' . wp_get_attachment_image_url($bc_grid['image'], 'large') . ')' : 'none'; ?>;
        --lbcg-grid-bg-pos: <?php echo !empty($bc_grid['bg_pos']) ? $bc_grid['bg_pos'] : '0 0'; ?>;
        --lbcg-grid-bg-repeat: <?php echo !empty($bc_grid['repeat']) ? $bc_grid['repeat'] : 'no-repeat'; ?>;
        --lbcg-grid-bg-size: <?php echo !empty($bc_grid['bg_size']) ? $bc_grid['bg_size'] : 'auto'; ?>;
        --lbcg-grid-bg-color: <?php echo !empty($bc_grid['color']) ? $bc_grid['color'] : '#003221'; ?>;
        --lbcg-grid-bg-opacity: <?php echo isset($bc_grid['opacity']) ? $bc_grid['opacity'] / 100 : .5; ?>;
    }
</style>