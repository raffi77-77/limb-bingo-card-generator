<?php
/**
 * The Template for displaying bingo card invitation page
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
$cu_email = '';
if ( is_user_logged_in() ) {
	global $current_user;
	$cu_email = $current_user->user_email;
}
$public_instance = LBCG_Public::get_instance();
if ( $card_id = $public_instance->get_dev_mode_card_id() ) {
	$bc_permalink = get_permalink( $card_id );
	// Get bingo card data
	$data = $public_instance->get_post_data();
	// Type, size, title
	$bingo_card_type  = $data['bingo_card_type'][0];
	$bingo_grid_size  = $data['bingo_grid_size'][0];
	$bingo_card_title = $data['bingo_card_title'][0];
	// Special title
	if ( $bingo_card_type === '1-75' ) {
		$bingo_card_spec_title = ! empty( $data['bingo_card_spec_title'][0] ) ? str_split( $data['bingo_card_spec_title'][0] ) : [];
		$additional_spec_part  = '';
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
	// Header style
	$bc_header = unserialize( $data['bc_header'][0] );
	// Grid style
	$bc_grid = unserialize( $data['bc_grid'][0] );
	// Card style
	$bc_card = unserialize( $data['bc_card'][0] );
	// If include free space
	$bingo_grid_free_square = $bingo_grid_size !== '4x4' && $bingo_card_type !== '1-90' && $data['bingo_card_free_square'][0] === 'on';
	?>
    <input type="hidden" name="bingo_card_type" value="<?php echo $bingo_card_type; ?>">
    <div class="lbcg-custom-container">
        <main class="lbcg-parent lbcg-loading">
			<?php LBCG_Helper::show_breadcrumb( $lbcg_current_theme_name, get_the_ID(), 'bingo_theme' ); ?>
            <div class="lbcg-post-header">
                <h1><?php the_title(); ?></h1>
            </div>
            <div class="lbcg-invitation">
                <section class="lbcg-content">
                    <div class="lbcg-content-right">
                        <div class="lbcg-card-wrap">
                            <div class="lbcg-card">
                                <div class="lbcg-card-header-holder">
                                    <div class="lbcg-card-header">
                                        <span class="lbcg-card-header-text"><?php echo $bingo_card_title; ?></span>
                                    </div>
									<?php if ( $bingo_card_type === '1-75' ): ?>
                                        <div class="lbcg-card-subtitle">
                                            <span class="lbcg-card-subtitle-text"><?php
                                                echo $additional_spec_part;
                                                echo !empty( $bingo_card_spec_title ) ? '<span>' . implode( '</span><span>', $bingo_card_spec_title ) . '</span>' : '';
                                                echo $additional_spec_part; ?></span>
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

                <aside class="lbcg-sidebar">
                    <div class="lbcg-sidebar-form">
                        <form id="lbcg-view-all-cards-form" action="<?php echo $bc_permalink . 'all/'; ?>" method="get"
                              target="_blank">
                            <div class="lbcg-input-wrap">
                                <h3 class="lbcg-input-wrap-head">Print Cards</h3>
                            </div>
                            <div class="lbcg-input-wrap">
                                <label for="lbcg-cards-count" class="lbcg-label">How many cards would you like to print?</label>
                                <select name="bcc" id="lbcg-cards-count" class="lbcg-select">
                                    <option value="30" selected>30 cards</option>
                                    <option value="100">100 cards</option>
                                    <option value="250">250 cards</option>
                                    <option value="500">500 cards</option>
                                </select>
                            </div>
                            <div class="lbcg-input-wrap">
                                <label for="lbcg-cards-custom-count" class="lbcg-label">Custom number to print</label>
                                <input type="number" id="lbcg-cards-custom-count" class="lbcg-input" name="bcc" value=""
                                       min="0">
                            </div>
                            <div class="lbcg-input-wrap">
                                <label for="lbcg-cards-per-page" class="lbcg-label">How many cards per page?</label>
                                <select name="bcs" id="lbcg-cards-per-page"
                                        class="lbcg-select" <?php echo $bingo_card_type === '1-90' ? 'disabled' : ''; ?>>
                                    <option value="1">1 large card</option>
                                    <option value="2" selected="selected">2 medium cards</option>
                                    <option value="4">4 small cards</option>
                                </select>
                            </div>
                            <div class="lbcg-input-wrap lbcg-buttons-wrap">
                                <button id="lbcg-view-all-cards" class="lbcg-btn lbcg-btn--lg lbcg-btn--main"
                                        type="submit">Generate Printable Cards
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="lbcg-sidebar-form">
                        <form id="lbcg-bc-invitation" action="<?php echo admin_url( 'admin-ajax.php' ); ?>"
                              method="post">
                            <input type="hidden" name="action" value="lbcg_bc_invitation">
                            <input type="hidden" name="bingo_card_uid" value="<?php echo $_GET['bc']; ?>">
                            <div class="lbcg-content-form">
                                <div class="lbcg-input-wrap">
                                    <h3 class="lbcg-input-wrap-head">Invite through email</h3>
                                </div>
                                <div class="lbcg-input-wrap">
                                    <label for="cu-email" class="lbcg-label">Your email:</label>
                                    <input class="lbcg-input" type="email" id="cu-email" name="author_email"
                                           value="<?php echo $cu_email; ?>"
                                           placeholder="Enter email">
                                </div>
                                <div class="lbcg-input-wrap">
                                    <label for="invite-emails" class="lbcg-label">Invite emails:</label>
                                    <textarea class="lbcg-input" id="invite-emails" name="invite_emails"
                                              cols="" rows="6"
                                              placeholder="Enter invite emails, one per line e.g.&#13;&#10;info@test.com&#13;&#10;john@smith.com"></textarea>
                                </div>
                            </div>
                            <div class="lbcg-input-wrap lbcg-buttons-wrap">
                                <a class="lbcg-btn lbcg-btn--lg lbcg-btn--back" role="button" role="button"
                                   href="<?php echo get_permalink( get_the_ID() ) . '?bc=' . $_GET['bc']; ?>">Back</a>
                                <button class="lbcg-btn lbcg-btn--lg lbcg-btn--main" type="submit">Invite</button>
                            </div>
                        </form>
                    </div>
                </aside>
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
