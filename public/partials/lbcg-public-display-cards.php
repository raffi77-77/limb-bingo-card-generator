<?php
/**
 * The Template for displaying all generated bingo cards
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$lbcg_current_theme_name = wp_get_theme()->get( 'Name' );
if ( $lbcg_current_theme_name === 'BNBS' ) {
	// For BNBS theme
	$tpl             = 'single-bingo-card';
	$lyt             = 'single-bingo-card';
	$ftd_pagetyp     = 'custom-post';
	$body_page_class = $tpl;
	require( get_template_directory() . '/header.php' );
} else {
	get_header();
}
$data = LBCG_Public::get_instance()->get_post_data();
// Get bingo card data
$title             = $data['bingo_card_title'][0];
$type              = $data['bingo_card_type'][0];
$single_page_count = isset( $_GET['bcs'] ) && $type !== '1-90' ? (int) $_GET['bcs'] : 2;
$cards_count       = (int) $_GET['bcc'];
// If put valid params
if ( $cards_count < 1 || $cards_count > 500 || ! in_array( $single_page_count, [ 1, 2, 4 ] ) ) {
	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	get_template_part( 404 );
	exit();
}
// Get generated contents
$all = LBCG_Helper::generate_all_content_info( get_the_ID(), 500, $cards_count, $data );
//$needed_contents = array_slice( $all, 0, $cards_count );
if ( $type === '1-75' ) {
	$spec_title           = ! empty( $data['bingo_card_spec_title'][0] ) ? str_split( $data['bingo_card_spec_title'][0] ) : [];
	$additional_spec_part = '';
	switch ( count( $spec_title ) ) {
		case 4:
		case 3:
			$additional_spec_part = '<span></span>';
			break;
		case 2:
		case 1:
			$additional_spec_part = '<span></span><span></span>';
			break;
	}
}
// Free square
if ( ! empty( $data['bingo_card_free_square'][0] ) && $data['bingo_card_free_square'][0] === 'on' && $data['bingo_grid_size'][0] !== '4x4' && $type !== '1-90' ) {
	$bingo_grid_free_square = true;
} else {
	$bingo_grid_free_square = false;
}
if ( $type !== '1-75' && $type !== '1-90' ) {
	$bingo_card_content = explode( "\r\n", $data['bingo_card_content'][0] );
}
// Create bingo card header
ob_start();
?>
    <div class="lbcg-card-header-holder">
        <div class="lbcg-card-header">
            <span class="lbcg-card-header-text"><?php echo $title; ?></span>
        </div>
		<?php if ( $type === '1-75' ): ?>
            <div class="lbcg-card-subtitle">
                <span class="lbcg-card-subtitle-text"><?php
                    echo $additional_spec_part;
                    echo ! empty( $spec_title ) ? '<span>' . implode( '</span><span>', $spec_title ) . '</span>' : '';
                    echo $additional_spec_part; ?></span>
            </div>
		<?php endif; ?>
    </div>
<?php
$card_header_html = ob_get_clean();
?>
    <style type="text/css" media="print">
        @page {
            size: <?php echo $single_page_count === 2 && $type !== '1-90' ? 'A4 landscape' : 'A4 portrait'; ?>;
        }
    </style>
    <input type="hidden" name="bingo_card_type" value="<?php echo $type; ?>">
    <div class="lbcg-custom-container">
        <main class="lbcg-parent lbcg-loading">
            <div class="lbcg-social-content print-version">
		        <?php $share_url = add_query_arg( [
			        'bcc' => $single_page_count,
			        'bcs' => $cards_count,
		        ], get_permalink( get_the_ID() ) . 'all' );
		        LBCG_Public::get_instance()->show_social_container( $share_url ); ?>
            </div>
            <?php
			$all_k = 0;
			while ( $all_k < $cards_count ): ?>
                <div class="lbcg-print-wrap lbcg-print-wrap-<?php echo $single_page_count; ?>">
					<?php
					$all_i = 0;
					do { ?>
                        <div class="lbcg-print-wrap-in">
                            <div class="lbcg-print-wrap-card-holder">
                                <div class="lbcg-card-wrap">
                                    <div class="lbcg-card">
										<?php echo $card_header_html; ?>
                                        <div class="lbcg-card-body">
											<?php
											if ( $type === '1-90' ) {
												$bingo_card_words = explode( ':', $all[ $all_k ] );
												foreach ( $bingo_card_words as $single_card_words ) {
													$single_card_words = explode( ';', $single_card_words );
													?>
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
											} else {
												$bingo_card_words = explode( ';', $all[ $all_k ] );
												?>
                                                <div class="lbcg-card-body-grid lbcg-grid-<?php echo $data['bingo_grid_size'][0][0]; ?>">
													<?php
													$grid_sq_count = $data['bingo_grid_size'][0][0] ** 2;
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
                                                                    echo isset( $bingo_card_content ) ? $bingo_card_content[ $bingo_card_words[ $i - 1 ] ] : $bingo_card_words[ $i - 1 ];
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
                        </div>
						<?php
						if ( ++ $all_k >= $cards_count ) {
							break;
						}
					} while ( ++ $all_i % $single_page_count > 0 ) ?>
                </div>
				<?php
				if ( $all_k >= $cards_count ) {
					break;
				}
			endwhile; ?>
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
