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
$the_taxonomy = get_queried_object();
?>
    <input type="hidden" name="bingo_card_type" value="general">
    <div class="lbcg-custom-container">
        <main class="lbcg-parent">
			<?php LBCG_Helper::show_breadcrumb( $lbcg_current_theme_name, $the_taxonomy, 'ubud_category' ); ?>
            <div class="lbcg-post-header">
                <h1><?php echo $the_taxonomy->name; ?></h1>
            </div>
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
                        <div class="lbcg-sidebar-in <?php echo $the_taxonomy->term_id === $cur_term_id ? 'collapsed' : ''; ?>">
                            <div class="lbcg-sidebar-header">
                                <a href="<?php echo get_term_link( $cur_term_id ); ?>"
                                   class="lbcg-sidebar-btn <?php echo $the_taxonomy->term_id === $cur_term_id ? 'active' : ''; ?>"><?php echo $taxonomies[ $cur_term_id ]->name; ?></a>
                                <span class="lbcg-sidebar-arrow"></span>
                            </div>
                            <div class="lbcg-sidebar-body">
								<?php
								foreach ( $menu_item as $bingo_theme ) {
									?>
                                    <a href="<?php echo esc_url( get_permalink( $bingo_theme->ID ) ); ?>" class="lbcg-sidebar-link"><?php echo $bingo_theme->post_title; ?></a>
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
					$bingo_themes = new WP_Query( array(
						'post_type'      => 'bingo_theme',
						'post_status'    => 'publish',
						'orderby'        => 'post_title',
						'order'          => 'ASC',
						'posts_per_page' => 6,
						'paged'          => isset( $_GET['tab'] ) ? $_GET['tab'] : 1,
						'tax_query'      => array(
							array(
								'taxonomy' => 'ubud-category',
								'field'    => 'term_id',
								'terms'    => $the_taxonomy->term_id
							)
						)
					) );
					$k            = 0;
					while ( $k < count( $bingo_themes->posts ) ) {
						$j = 0;
						?>
                        <div class="lbcg-generators-row<?php echo count( $bingo_themes->posts ) > 1 ? '-2' : ''; ?>">
							<?php
							do {
								$bingo_theme = $bingo_themes->posts[ $k ];
								?>
                                <div class="lbcg-generators-single">
                                    <div class="lbcg-generators-image">
                                        <img src="<?php echo esc_url( wp_get_attachment_image_url( get_post_thumbnail_id( $bingo_theme->ID ), 'medium' ) ); ?>" alt="<?php echo $bingo_theme->post_title; ?>">
                                    </div>
                                    <div class="lbcg-generators-title">
                                        <a href="<?php echo get_permalink( $bingo_theme->ID ); ?>"><?php echo $bingo_theme->post_title; ?></a>
                                    </div>
									<?php if ( $text = get_post_meta( $bingo_theme->ID, 'bt_intro_text', true ) ): ?>
                                        <div class="lbcg-generators-content">
											<?php echo $text; ?>
                                        </div>
									<?php endif; ?>
                                </div>
								<?php
								if ( ++ $k >= count( $bingo_themes->posts ) ) {
									break;
								}
							} while ( ++ $j < 2 );
							?>
                        </div>
						<?php
					}
					$max_num_pages = $bingo_themes->max_num_pages;
					$paged         = $bingo_themes->query_vars['paged'];
					if ( $max_num_pages > 1 ) {
						if ( $paged > 1 ) {
							$page_items = [ $paged - 1, $paged ];
							$last_item = $paged;
						} else {
							$page_items = [ $paged, $paged + 1 ];
							$last_item = $paged + 1;
						}
						if ( $max_num_pages > $last_item ) {
							$page_items[] = $last_item + 1;
						} else if ( $paged > 1 ) {
							array_unshift($page_items, $page_items[0] - 1 );
						}
						?>
                        <div class="lbcg-pagination">
                            <ul>
                                <li class="page-item <?php echo $paged < 2 ? 'disabled' : ''; ?>">
                                    <a href="?tab=<?php echo $paged > 1 ? $paged - 1 : 1; ?>" class="page-link"><</a>
                                </li>
								<?php
								foreach ( $page_items as $item ) { ?>
                                    <li class="page-item <?php echo $item === $paged ? 'active' : ''; ?>">
                                        <a href="?tab=<?php echo $item; ?>" class="page-link"><?php echo $item; ?></a>
                                    </li>
									<?php
								}
								?>
                                <li class="page-item <?php echo $paged >= $max_num_pages ? 'disabled' : ''; ?>">
                                    <a href="?tab=<?php echo $paged < $max_num_pages ? $paged + 1 : $max_num_pages; ?>" class="page-link">></a>
                                </li>
                            </ul>
                        </div>
						<?php
					}
					?>
                </section>
            </div>
			<?php
//			$intro_text = get_term_meta( $the_taxonomy->term_id, 'lbcg_intro_text', true );
			if ( ! empty( $intro_text ) ): ?>
                <div class="lbcg-post-content"><?php echo $intro_text; ?></div>
			<?php endif; ?>
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
