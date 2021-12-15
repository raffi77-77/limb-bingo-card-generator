<?php

/**
 * The public-facing functionality of the plugin
 */
class LBCG_Public {
	/**
	 * Plugin all needed properties in one place
	 *
	 * @var array $attributes The array containing main attributes of the plugin
	 */
	protected $attributes = [];

	/**
	 * Construct Bingo Card Public object
	 *
	 * @param $attributes
	 */
	public function __construct( $attributes ) {
		$this->attributes = $attributes;
	}

	/**
	 * Add actions, filters ...
	 */
	public function register_dependencies() {
		add_filter( 'single_template', array( $this, 'get_custom_post_type_template' ) );
		add_filter( 'taxonomy_template', array( $this, 'get_custom_taxonomy_template' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_and_styles' ), 10 );
		add_action( 'wp_head', array( $this, 'add_custom_css' ) );
	}

	/**
	 * Enqueue public scripts and styles
	 */
	public function enqueue_scripts_and_styles() {
		if ( is_singular( 'bingo_theme' ) || is_singular( 'bingo_card' ) ) {
			wp_enqueue_script( 'lbcg-vanilla-js', $this->attributes['includes_url'] . 'js/vanilla.js' );
			/*if (!did_action('wp_enqueue_media')) {
				wp_enqueue_media();
			}*/
			wp_enqueue_script( 'lbcg-public-js', $this->attributes['public_url'] . 'js/lbcg-public.js', [], $this->attributes['plugin_version'] );
			wp_localize_script( 'lbcg-public-js', 'LBCG', [
				'fonts'          => LBCG_Helper::$fonts,
				'freeSquareWord' => LBCG_Helper::$free_space_word,
				'ajaxUrl'        => admin_url( 'admin-ajax.php' )
			] );

		}
        if ( is_singular( 'bingo_theme' ) || is_singular( 'bingo_card' ) || is_tax( 'ubud-category' ) ) {
	        wp_enqueue_style( 'lbcg-public-css', $this->attributes['public_url'] . 'css/lbcg-public.min.css?', [], $this->attributes['plugin_version'] );
        }
	}

	/**
	 * Get custom post type template
	 *
	 * @param   string  $single_template
	 *
	 * @return string
	 */
	public function get_custom_post_type_template( $single_template ) {
		global $post_type;
		if ( $post_type === 'bingo_theme' ) {
			if ( preg_match( '/bingo-card-generator\/([^\/]+)\/([^\/]+)\/invitation\/\?bc=([a-zA-z0-9]+)$/', $_SERVER['REQUEST_URI'] ) ) {
				$single_template = $this->attributes['public_templates_path'] . '/lbcg-public-display-invitation.php';
			} else {
				$single_template = $this->attributes['public_templates_path'] . '/lbcg-public-display-generator.php';
			}
		} elseif ( $post_type === 'bingo_card' ) {
			if ( preg_match( '/ubud-bingo-card\/([^\/]+)\/all\/\?([^\/]+)\/?$/', $_SERVER['REQUEST_URI'] ) ) {
				$single_template = $this->attributes['public_templates_path'] . '/lbcg-public-display-cards.php';
			} else {
				$single_template = $this->attributes['public_templates_path'] . '/lbcg-public-display-card.php';
			}
		}

		return $single_template;
	}

	/**
	 * Get custom taxonomy template
	 *
	 * @param   string  $template
	 *
	 * @return string
	 */
	public function get_custom_taxonomy_template( $template ) {
		if ( is_tax( 'ubud-category' ) ) {
			$template = $this->attributes['public_templates_path'] . '/lbcg-public-display-archive.php';
		}

		return $template;
	}

	/**
	 * Add custom css
	 */
	public function add_custom_css() {
		global $post;
		if ( $post instanceof WP_Post && ( $post->post_type === 'bingo_theme' || $post->post_type === 'bingo_card' ) ) {
			// Load google fonts
			?>
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
			<?php foreach ( LBCG_Helper::$fonts as $font ): ?>
                <link href="<?php echo $font['url'] ?>" rel="stylesheet">
			<?php endforeach;
			if ( ! empty( $_GET['bc'] ) ) {
				$bc_posts = get_posts( [
					'name'           => $_GET['bc'],
					'post_type'      => 'bingo_card',
					'posts_per_page' => 1,
					'post_status'    => 'publish',
				] );
				if ( ! empty( $bc_posts[0]->ID ) ) {
					$data = get_post_meta( $bc_posts[0]->ID );
				}
			}
			if ( empty( $data ) ) {
				$data = get_post_meta( $post->ID );
			}
			// Load attributes
			$grid_square = unserialize( $data['grid_square'][0] );
			$bc_header = unserialize( $data['bc_header'][0] );
			$bc_grid   = unserialize( $data['bc_grid'][0] );
			$bc_card   = unserialize( $data['bc_card'][0] );
			include_once $this->attributes['public_templates_path'] . '/lbcg-public-properties.php';
			// Load custom css
			?>
            <style type="text/css">
                <?php echo trim( wp_strip_all_tags( $data['bingo_card_custom_css'][0] ) ); ?>
            </style>
			<?php
		}
	}
}