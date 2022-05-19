<?php
/**
 * The Template for displaying bingo card generations page
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Shortcode template
if ( ! empty( $attributes ) ) {
	if ( isset( $attributes['slugs'] ) ) {
		$term_slugs = str_replace( ' ', '', $attributes['slugs'] );
		if ( ! empty( $term_slugs ) ) {
			$term_slugs = explode( ',', $term_slugs );
		}
	} else {
		$term_slugs = [];
	}
	// Get generators
	$paged          = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
	$posts_per_page = get_query_var( 'posts_per_page' ) ? get_query_var( 'posts_per_page' ) : 10; // 10 - Also please check $bta_per_page property of the public class
	$offset         = ( $paged - 1 ) * $posts_per_page;
	global $lc_max_page_numbers;
	if ( ! $lc_max_page_numbers ) {
		$total_count         = (int) wp_count_terms( [ 'taxonomy' => 'ubud-category', 'hide_empty' => false ] );
		$lc_max_page_numbers = round( $total_count / $posts_per_page );
	}
	$term_args = [
		'taxonomy'   => 'ubud-category',
//		'orderby'    => '_lc_meta_value', // Custom orderby field
//		'order'      => 'DESC',
		'number'     => $posts_per_page,
		'offset'     => $offset,
		'hide_empty' => false,
//        'meta_query' => [ [ 'key' => '_lbcg_created_at', 'type' => 'NUMERIC' ] ],
	];
	if ( ! empty( $term_slugs ) ) {
		$term_args['slug'] = $term_slugs;
	}
	$ubud_cats = get_terms( $term_args );
	// Show terms
	?>
    <div class="lbcg-tcs">
		<?php foreach ( $ubud_cats as $cat ): ?>
            <div class="lbcg-tcs-single">
                <div class="lbcg-tcs-thumb">
					<?php $thumb_id = get_term_meta( $cat->term_id, '_lbcg_thumbnail_id', true ); ?>
                    <img src="<?php echo esc_url( wp_get_attachment_image_url( $thumb_id, 'medium' ) ); ?>"
                         alt="<?php echo $cat->name; ?>">
                </div>
                <div class="lbcg-tcs-content">
                    <h2 class="lbcg-tcs-content-header">
                        <a href="<?php echo get_term_link( $cat->term_id ); ?>"><?php echo $cat->name; ?></a>
                    </h2>
					<?php if ( $intro_text = get_term_meta( $cat->term_id, 'lbcg_intro_text', true ) ) {
						echo '<div class="lbcg-tcs-content-body">' . $intro_text . '</div>';
					} ?>
                </div>
            </div>
		<?php endforeach; ?>
    </div>
	<?php
	// Pagination
	if ( $lc_max_page_numbers > 1 ) {
		?>
        <div class="lbcg-pagination">
			<?php
			pagination( $lc_max_page_numbers );
			?>
        </div>
		<?php
	}
}
