<?php
/**
 * The Template for displaying bingo_theme post type archive page
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$the_page = get_posts( [
	'name'        => 'bingo-cards',
	'post_type'   => 'page',
	'post_status' => 'draft',
	'numberposts' => 1
] );
if ( isset( $the_page[0] ) && $the_page[0] instanceof WP_Post ) {
	$the_content = apply_filters( 'the_content', $the_page[0]->post_content );
} else {
	$the_content = '';
}
$lbcg_current_theme_name = wp_get_theme()->get( 'Name' );
if ( $lbcg_current_theme_name === 'BNBS' ) {
	// For BNBS theme header
	global $wp;
	$tpl             = 'page';
	$lyt             = 'page';
	$ftd_pagetyp     = 'page'; // page, category, article, review, author, post, category-post
	$body_page_class = $tpl;
	$page_config     = add_query_arg( array(), $wp->request );
//	while (have_posts()) : the_post();
//		$post_id    = get_the_id();
//        echo '7777 = "' . $post_id . '"; ';
//		$title      = get_the_title();
//		$content    = get_the_content();
//	endwhile;
//	require( get_template_directory() . '/variables/' . $lyt . '.php' );
	$schema_type             = 'Article';
	$sch_page_url            = home_url( $page_config ) . '/';
	$sch_headline            = 'Bingo Card Generators';
	$sch_alternativeHeadline = 'Bingo Card Generators';
	$sch_description         = $the_content;
	$sch_articleBody         = $the_content;
	// Breadcrumbs
	$breadcrumbs      = array( 'Home' => get_site_url() . '/', $sch_headline => '' );
	$articleSection   = '';
	$breadcrumb_items = '';
	$i                = 1;
	foreach ( $breadcrumbs as $name => $link ) {
		if ( $i == 1 ) {
			$slash = '';
		} else {
			$slash = '/';
		}
		if ( $i != 2 ) {
			$articleSection .= $slash . $name;
			$comma          = ',';
			$link_url       = $link;
		} else {
			$comma    = '
                ';
			$link_url = $sch_page_url;
		}
		$breadcrumb_items .= '
                    {"@type": "ListItem",
                    "position" : ' . $i . ',
                    "name" : "' . $name . '",
                    "item" : "' . $link_url . '"}' . $comma;
		$i ++;
	}
	require( get_template_directory() . '/scripts/schema.php' );
	require( get_template_directory() . '/header.php' );
} else {
	get_header();
}
?>
    <main class="order-containers d-flex flex-column">
        <div class="layout--container-fluid order-1">
            <div class="layout--container mt-4">
				<?php if ( $lbcg_current_theme_name === 'BNBS' ) {
					require( get_template_directory() . '/layouts/comp/breadcrumbs.php' );
				} ?>
                <div class="layout--row">
                    <article class="main layout--col pt-0 pt-4">
                        <div class="pt-4 px-4">
                            <div class="title--head">
                                <h1>Bingo Card Generators</h1>
                            </div>
                        </div>
                        <hr>
                        <div class="wysiwyg theme theme-light py-4 px-4">
							<?php echo $the_content; ?>
                        </div>
                    </article>
					<?php if ( $lbcg_current_theme_name === 'BNBS' ) {
						require( THEMEPATH . '/layouts/sidebar/sidebar.php' );
					} ?>
                </div>
            </div>
        </div>
    </main>
<?php
if ( $lbcg_current_theme_name === 'BNBS' ) {
	$data = array( 'footer' => array() );
	require( get_template_directory() . '/models/m-partials.php' );
	$footer = $data['footer'];
	require( get_template_directory() . '/footer.php' );
} else {
	get_footer();
}
