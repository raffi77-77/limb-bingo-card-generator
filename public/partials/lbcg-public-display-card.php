<?php
/**
 * The Template for displaying bingo card
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
$public_instance = LBCG_Public::get_instance();
$data = $public_instance->get_post_data();
if ( ! empty( $data['bingo_card_own_content'][0] ) ) {
	// Type, size, title
	$bingo_card_type  = $data['bingo_card_type'][0];
	$bingo_grid_size  = $data['bingo_grid_size'][0];
	$bingo_card_title = $data['bingo_card_title'][0];
	// Special title
	$additional_spec_part  = '';
	if ( $bingo_card_type === '1-75' ) {
		$bingo_card_spec_title = ! empty( $data['bingo_card_spec_title'][0] ) ? str_split( $data['bingo_card_spec_title'][0] ) : [];
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
	} else {
		$bingo_card_spec_title = [ 'B', 'I', 'N', 'G', 'O' ];
    }
	// Bingo card words
	if ( $bingo_card_type === '1-90' ) {
		$bingo_card_words = explode( ':', $data['bingo_card_own_content'][0] );
		foreach ( $bingo_card_words as $key => $value ) {
			$bingo_card_words[ $key ] = explode( ';', $value );
		}
	} else {
		$bingo_card_words = explode( "\r\n", $data['bingo_card_own_content'][0] );
	}
	// If include free space
	$bingo_grid_free_square = $bingo_grid_size !== '4x4' && $bingo_card_type !== '1-90' && $data['bingo_card_free_square'][0] === 'on';
    // Card
	$card = get_post( get_the_ID() );
	?>
    <input type="hidden" name="bingo_card_type" value="<?php echo $bingo_card_type; ?>">
    <div class="lbcg-custom-container">
        <main class="lbcg-parent lbcg-loading">
            <div class="lbcg-card-view">
                <div class="lbcg-card-wrap">
                    <div class="lbcg-social-content">
		                <?php $share_url = get_permalink( $card->ID );
		                $share_media_url = get_the_post_thumbnail_url( $card->ID );
                        $public_instance->show_social_container( $share_url, $share_media_url ); ?>
                    </div>
                    <div class="lbcg-input-wrap lbcg-buttons-wrap">
                        <a class="lbcg-btn lbcg-btn--lg lbcg-btn--main lbcg-custom-btn" role="button"
                           href="<?php echo home_url( 'bingo-cards' ); ?>">See All Bingo Cards</a>
                    </div>
                    <div class="lbcg-card">
                        <input type="hidden" id="bc-pn" value="<?php echo $card->post_name; ?>">
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
                                $i = 1;
								foreach ( $bingo_card_words as $single_card_words ) { ?>
                                    <div class="lbcg-card-body-grid lbcg-grid-9">
										<?php
										foreach ( $single_card_words as $key => $number ) { ?>
                                            <div class="lbcg-card-col" data-key="<?php echo $i++; ?>">
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
                                        <div class="lbcg-card-col<?php echo $is_free_space ? ' lbcg-free-space' : ''; ?>" data-key="<?php echo $i; ?>">
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
			<?php $the_content = get_the_content();
			if ( ! empty( $the_content ) ): ?>
                <div class="lbcg-post-content">
					<?php the_content(); ?>
                </div>
			<?php endif; ?>
        </main>
    </div>
	<?php
} else {
	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	get_template_part( 404 );
	exit();
}
if ( $lbcg_current_theme_name === 'BNBS' ) {
	$data = array( 'footer' => array() );
	require( get_template_directory() . '/models/m-partials.php' );
	$footer = $data['footer'];
	require( get_template_directory() . '/footer.php' );
} else {
	get_footer();
}
