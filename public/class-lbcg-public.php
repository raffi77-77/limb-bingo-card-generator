<?php

/**
 * The public-facing functionality of the plugin
 */
class LBCG_Public {
	/**
	 * An instance of this class
	 *
	 * @var LBCG_Public|null $instance An object of this class
	 */
	private static $instance = null;

	/**
	 * Plugin all needed properties in one place
	 *
	 * @var array $attributes The array containing main attributes of the plugin
	 */
	protected $attributes = [];

	/**
	 * Current object data
	 *
	 * @var array $data The array containing all metadata of current post
	 */
	private $data = null;

	/**
	 * If current card in dev mode
	 *
	 * @var bool $dev_mode_card_id Card mode
	 */
	private $dev_mode_card_id = 0;

	/**
	 * Get instance
	 *
	 * @param $attributes
	 *
	 * @return LBCG_Public|null
	 */
	public static function get_instance( $attributes = [] ) {
		if ( null === self::$instance && ! empty( $attributes ) ) {
			// Create new instance
			self::$instance = new LBCG_Public( $attributes );
		}

		return self::$instance;
	}

	/**
	 * Construct Bingo Card Public object
	 *
	 * @param   array  $attributes
	 */
	private function __construct( $attributes ) {
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
		if ( is_singular( [ 'bingo_theme', 'bingo_card' ] ) || is_tax( 'ubud-category' ) ) {
			wp_enqueue_script( 'lbcg-vanilla-js', $this->attributes['includes_url'] . 'js/vanilla.js' );
			wp_enqueue_script( 'lbcg-public-js', $this->attributes['public_url'] . 'js/lbcg-public.min.js', [], $this->attributes['plugin_version'] );
			wp_localize_script( 'lbcg-public-js', 'LBCG', [
				'fonts'          => LBCG_Helper::$fonts,
				'freeSquareWord' => LBCG_Helper::$free_space_word,
				'ajaxUrl'        => admin_url( 'admin-ajax.php' )
			] );
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
		if ( is_singular( 'bingo_theme' ) ) {
			if ( preg_match( '/bingo-card-generator\/([^\/]+)\/([^\/]+)\/invitation\/\?bc=([a-zA-z0-9]+)$/', $_SERVER['REQUEST_URI'] ) ) {
				$single_template = $this->attributes['public_templates_path'] . '/lbcg-public-display-invitation.php';
			} else {
				$single_template = $this->attributes['public_templates_path'] . '/lbcg-public-display-generator.php';
			}
		} elseif ( is_singular( 'bingo_card' ) ) {
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
	 * Get current post metadata
	 *
	 * @return array|null
	 */
	public function get_post_data() {
		return $this->data;
	}

	/**
	 * Get current being built card id
	 *
	 * @return int
	 */
	public function get_dev_mode_card_id() {
		return $this->dev_mode_card_id;
	}

	/**
	 * Add custom css
	 */
	public function add_custom_css() {
		if ( is_singular( [ 'bingo_theme', 'bingo_card' ] ) ) {
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
					$this->dev_mode_card_id = $bc_posts[0]->ID;
					$this->data             = get_post_meta( $bc_posts[0]->ID );
				}
			}
			if ( empty( $this->data ) ) {
				$this->data = get_post_meta( get_the_ID() );
			}
			// Load attributes
			$grid_square = unserialize( $this->data['grid_square'][0] );
			$bc_header   = unserialize( $this->data['bc_header'][0] );
			$bc_grid     = unserialize( $this->data['bc_grid'][0] );
			$bc_card     = unserialize( $this->data['bc_card'][0] );
			$data        = $this->data;
			include_once $this->attributes['public_templates_path'] . '/lbcg-public-properties.php';
			// Load custom css
			?>
            <style type="text/css">
                <?php echo trim( wp_strip_all_tags( $this->data['bingo_card_custom_css'][0] ) ); ?>
            </style>
			<?php
		}
	}
}