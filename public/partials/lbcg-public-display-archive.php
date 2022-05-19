<?php
/**
 * The Template for displaying bingo theme archive
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
$the_term = get_queried_object();
?>
    <input type="hidden" name="bingo_card_type" value="general">
    <div class="lbcg-custom-container">
        <main class="lbcg-parent">
			<?php LBCG_Helper::show_breadcrumb( $lbcg_current_theme_name, $the_term, 'ubud_category' ); ?>
            <div class="lbcg-post-header">
                <h1><?php echo $the_term->name; ?></h1>
            </div>
			<?php $intro_text = get_term_meta( $the_term->term_id, 'lbcg_intro_text', true );
			if ( ! empty( $intro_text ) ): ?>
                <div class="lbcg-post-content lbcg-intro-text"><?php echo $intro_text; ?></div>
			<?php endif; ?>
            <div class="lbcg-main">
                <aside class="lbcg-sidebar">
					<?php
					$all_bingo_themes = get_posts( [
						'post_type'   => 'bingo_theme',
						'post_status' => 'publish',
						'numberposts' => - 1,
						'orderby'     => 'post_title',
						'order'       => 'ASC'
					] );
					$menu_items       = [];
					$taxonomies       = [];
					foreach ( $all_bingo_themes as $bingo_theme ) {
						$bt_categories = get_the_terms( $bingo_theme->ID, 'ubud-category' );
						if ( ! empty( $bt_categories[0] ) ) {
							if ( ! isset( $taxonomies[ $bt_categories[0]->term_id ] ) ) {
								$taxonomies[ $bt_categories[0]->term_id ] = $bt_categories[0];
							}
							$menu_items[ $bt_categories[0]->term_id ][] = $bingo_theme;
						}
					}
					ksort( $menu_items );
					foreach ( $menu_items as $cur_term_id => $menu_item ) {
						?>
                        <div class="lbcg-sidebar-in <?php echo $the_term->term_id === $cur_term_id ? 'collapsed' : ''; ?>">
                            <div class="lbcg-sidebar-header">
                                <a href="<?php echo get_term_link( $cur_term_id ); ?>"
                                   class="lbcg-sidebar-btn <?php echo $the_term->term_id === $cur_term_id ? 'active' : ''; ?>"><?php echo $taxonomies[ $cur_term_id ]->name; ?></a>
                                <span class="lbcg-sidebar-arrow"></span>
                            </div>
                            <div class="lbcg-sidebar-body">
								<?php
								foreach ( $menu_item as $bingo_theme ) {
									?>
                                    <a href="<?php echo esc_url( get_permalink( $bingo_theme->ID ) ); ?>"
                                       class="lbcg-sidebar-link"><?php echo $bingo_theme->post_title; ?></a>
									<?php
								}
								?>
                            </div>
                        </div>
						<?php
					}
					?>
                </aside>
                <section class="lbcg-generators">
					<?php
					$paged                = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
					$posts_per_page       = get_query_var( 'posts_per_page' ) ? get_query_var( 'posts_per_page' ) : 6; // 6 - Also please check in `pre_get_posts_args` method of the public class
					$bingo_themes         = new WP_Query( array(
						'post_type'      => 'bingo_theme',
						'post_status'    => 'publish',
						'orderby'        => 'post_title',
						'order'          => 'ASC',
						'posts_per_page' => $posts_per_page,
						'paged'          => $paged,
						'tax_query'      => array(
							array(
								'taxonomy' => 'ubud-category',
								'field'    => 'term_id',
								'terms'    => $the_term->term_id
							)
						)
					) );
					$k                    = 0;
					$generators_row_class = 'lbcg-generators-row' . ( count( $bingo_themes->posts ) > 1 ? '-2' : '' );
					while ( $k < count( $bingo_themes->posts ) ) {
						$j             = 0;
						$descs_exists  = false;
						$descs_content = '';
						$cards_content = '';
						do {
							$bingo_theme = $bingo_themes->posts[ $k ];
							ob_start();
							?>
                            <div class="lbcg-generators-single">
								<?php //if ( $text = get_post_meta( $bingo_theme->ID, 'bt_intro_text', true ) ):
								//$descs_exists = true; ?>
                                <div class="lbcg-generators-content">
									<?php //echo $text; ?>
                                </div>
								<?php //endif; ?>
                            </div>
							<?php
							$descs_content .= ob_get_clean();
							ob_start();
							?>
                            <div class="lbcg-generators-single">
                                <div class="lbcg-generators-image">
                                    <img src="<?php echo esc_url( wp_get_attachment_image_url( get_post_thumbnail_id( $bingo_theme->ID ), 'medium' ) ); ?>"
                                         alt="<?php echo $bingo_theme->post_title; ?>">
                                </div>
                                <div class="lbcg-generators-title">
                                    <a href="<?php echo get_permalink( $bingo_theme->ID ); ?>"><?php echo $bingo_theme->post_title; ?></a>
                                </div>
                            </div>
							<?php
							$cards_content .= ob_get_clean();
							if ( ++ $k >= count( $bingo_themes->posts ) ) {
								break;
							}
						} while ( ++ $j < 2 );
						if ( $descs_exists === true ): ?>
                            <div class="<?php echo $generators_row_class; ?>">
								<?php echo $descs_content; ?>
                            </div>
						<?php endif; ?>
                        <div class="<?php echo $generators_row_class; ?>">
							<?php echo $cards_content; ?>
                        </div>
						<?php
					}
					global $wp_query;
					if ( $wp_query->max_num_pages > 1 ) {
						?>
                        <div class="lbcg-pagination">
							<?php pagination( $wp_query->max_num_pages ); ?>
                        </div>
						<?php
					}
					?>
                </section>
            </div>
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
