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
$taxonomy = get_queried_object();
// Get bingo themes
$bingo_themes = get_posts( array(
	'posts_per_page' => - 1,
	'post_type'      => 'bingo_theme',
	'post_status'    => 'publish',
	'tax_query'      => array(
		array(
			'taxonomy' => 'ubud-category',
			'field'    => 'term_id',
			'terms'    => $taxonomy->term_id
		)
	)
) );
?>
    <input type="hidden" name="bingo_card_type" value="general">
    <div class="lbcg-custom-container">
        <main class="lbcg-parent">
            <div class="lbcg-post-header">
                <h1><?php echo $taxonomy->name; ?></h1>
            </div>
			<?php
			$k = 0;
			while ( $k < count( $bingo_themes ) ) {
				?>
                <div class="lbcg-print-wrap lbcg-print-wrap-2"><?php
				$j = 0;
				do {
					$thumbnail_url = get_the_post_thumbnail_url( $bingo_themes[ $k ]->ID, 'full' );
					?>
                    <div class="lbcg-print-wrap-in">
                        <div class="lbcg-print-wrap-card-holder">
                            <a href="<?php echo get_permalink( $bingo_themes[ $k ]->ID ); ?>">
                                <img src="<?php echo $thumbnail_url; ?>" alt="<?php echo $bingo_themes[ $k ]->post_title; ?>" style="width: 100%;">
                            </a>
                        </div>
                    </div><?php
					if ( ++ $k >= count( $bingo_themes ) ) {
						break;
					}
				} while ( ++ $j < 2 );
				?></div><?php
			} ?>
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
