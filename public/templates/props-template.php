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
        --lbcg-header-font-family: '<?php echo !empty($data['bingo_card_font'][0]) ? BingoCardHelper::$fonts[$data['bingo_card_font'][0]]['name'] : 'Roboto'; ?>', sans-serif;
        --lbcg-header-text-color: #FFF;
        --lbcg-header-bg-image: <?php echo !empty($bc_header['image']) ? 'url(' . wp_get_attachment_image_url($bc_header['image'], 'large') . ')' : 'none' ?>;
        --lbcg-header-bg-pos: <?php echo !empty($bc_header['bg_pos']) ? $bc_header['bg_pos'] : '0 0'; ?>;
        --lbcg-header-bg-repeat: <?php echo !empty($bc_header['repeat']) ? $bc_header['repeat'] : 'no-repeat'; ?>;
        --lbcg-header-bg-size: <?php echo !empty($bc_header['bg_size']) ? $bc_header['bg_size'] : 'auto'; ?>;
        --lbcg-header-bg-color: <?php echo !empty($bc_header['color']) ? $bc_header['color'] : 'transparent'; ?>;
        --lbcg-header-bg-opacity: <?php echo isset($bc_header['opacity']) ? $bc_header['opacity'] / 100 : 1; ?>;

        /* body styles */

        --lbcg-grid-font-size: 16px; /* esi Vah jan gtnumes es <span class="lbcg-card-text"> srancic amenamec heightov@ U HAMEL amena erkar u dnumes es variable-i mej */
        --lbcg-grid-font-family: '<?php echo !empty($data['bingo_card_font'][0]) ? BingoCardHelper::$fonts[$data['bingo_card_font'][0]]['name'] : 'Roboto'; ?>', sans-serif;
        --lbcg-grid-line-height: <?php echo $data['bingo_card_type'][0] === '1-90' ? '34.3px' : '61.8px'; ?>; /* esi Vah jan vercnum es <div class="lbcg-card-col"> sra height@ u dnumes es variable-i mej */
        --lbcg-grid-text-color: #79ffd3;
        --lbcg-grid-border-color: #45ffbf;
        --lbcg-grid-bg-image: <?php echo !empty($bc_grid['image']) ? 'url(' . wp_get_attachment_image_url($bc_grid['image'], 'large') . ')' : 'none'; ?>;
        --lbcg-grid-bg-pos: <?php echo !empty($bc_grid['bg_pos']) ? $bc_grid['bg_pos'] : '0 0'; ?>;
        --lbcg-grid-bg-repeat: <?php echo !empty($bc_grid['repeat']) ? $bc_grid['repeat'] : 'no-repeat'; ?>;
        --lbcg-grid-bg-size: <?php echo !empty($bc_grid['bg_size']) ? $bc_grid['bg_size'] : 'auto'; ?>;
        --lbcg-grid-bg-color: <?php echo !empty($bc_grid['color']) ? $bc_grid['color'] : '#003221'; ?>;
        --lbcg-grid-bg-opacity: <?php echo isset($bc_grid['opacity']) ? $bc_grid['opacity'] / 100 : .5; ?>;
    }
</style>