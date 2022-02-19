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
	$total_count   = (int) wp_count_terms( [ 'taxonomy' => 'ubud-category', 'hide_empty' => false ] );
	$number        = 10;
	$max_num_pages = round( $total_count / $number );
	$offset        = ! empty( $_GET['tab'] ) && $_GET['tab'] <= $max_num_pages ? $_GET['tab'] - 1 : 0;
	$term_args     = [
		'taxonomy'   => 'ubud-category',
		'orderby'    => '_lc_meta_value', // Custom orderby field
		'order'      => 'DESC',
		'number'     => $number,
		'offset'     => $offset,
		'hide_empty' => false,
        'meta_query' => [ [ 'key' => '_lbcg_created_at', 'type' => 'NUMERIC' ] ],
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
                    <img src="<?php echo esc_url( wp_get_attachment_image_url( $thumb_id, 'medium' ) ); ?>" alt="<?php echo $cat->name; ?>">
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
	$paged = $offset + 1;
	if ( $max_num_pages > 1 ) {
		if ( $paged > 1 ) {
			$page_items = [ $paged - 1, $paged ];
			$last_item  = $paged;
		} else {
			$page_items = [ $paged, $paged + 1 ];
			$last_item  = $paged + 1;
		}
		if ( $max_num_pages > $last_item ) {
			$page_items[] = $last_item + 1;
		} elseif ( $paged > 1 ) {
			array_unshift( $page_items, $page_items[0] - 1 );
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
}
