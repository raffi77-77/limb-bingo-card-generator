<?php

/**
 * The admin-specific functionality of the plugin
 */
class LBCG_Admin {
	/**
	 * Plugin all needed properties in one place
	 *
	 * @var array $attributes The array containing main attributes of the plugin
	 */
	protected $attributes = [];

	/**
	 * Construct Bingo Card Admin object
	 *
	 * @param   array  $attributes
	 */
	public function __construct( $attributes ) {
		$this->attributes = $attributes;
	}

	/**
	 * Add actions, filters ...
	 */
	public function register_dependencies() {
		add_action( 'add_meta_boxes', array( $this, 'add_custom_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_custom_fields' ), 10, 3 );
		add_action( 'post_updated_messages', array( $this, 'show_editor_message' ) );
		add_action( 'admin_print_scripts-post-new.php', array( $this, 'enqueue_admin_script_and_styles' ), 11 );
		add_action( 'admin_print_scripts-post.php', array( $this, 'enqueue_admin_script_and_styles' ), 11 );
		add_action( 'admin_head', array( $this, 'add_custom_css' ) );
		$this->disable_emojy_print();
	}

	/**
	 * Add meta boxes
	 */
	public function add_custom_meta_boxes() {
		add_meta_box( 'bingo-theme-custom-fields', 'Custom Fields (set up default values)', array( $this, 'get_bingo_theme_custom_fields_template' ), 'bingo_theme' );
		add_meta_box( 'bingo-theme-custom-fields', 'Custom Fields', array( $this, 'get_bingo_card_custom_fields_template' ), 'bingo_card' );
	}

	/**
	 * Get bingo theme custom fields template
	 */
	public function get_bingo_theme_custom_fields_template() {
		include( $this->attributes['admin_templates_path'] . '/lbcg-admin-display.php' );
	}

	/**
	 * Get bingo card custom fields template
	 */
	public function get_bingo_card_custom_fields_template() {
		include( $this->attributes['admin_templates_path'] . '/lbcg-admin-display.php' );
	}

	/**
	 * Save custom fields
	 *
	 * @param   int      $post_id
	 * @param   WP_Post  $post
	 * @param   bool     $update
	 */
	public function save_custom_fields( $post_id, $post, $update ) {
		if ( $post->post_type === 'bingo_theme' || $post->post_type === 'bingo_card' ) {
			$this->save_bingo_custom_fields( $post_id );
		}
	}

	/**
	 * Save bingo theme custom fields
	 *
	 * @param   int  $post_id
	 */
	private function save_bingo_custom_fields( $post_id ) {
		if ( ! empty( $_POST['lbcg_action'] ) && $_POST['lbcg_action'] === 'save_bc_post' ) {
			LBCG_Helper::save_bingo_meta_fields( $post_id, $_POST );
		}
	}

	/**
	 * Set error messages
	 *
	 * @param   array  $messages
	 *
	 * @return array
	 */
	public function show_editor_message( $messages ) {
		global $post;
		if ( ( $post->post_type === 'bingo_theme' || $post->post_type === 'bingo_card' ) && $post->post_status !== 'auto-draft' ) {
			$post_meta = get_post_meta( $post->ID );
			// Collecting errors
			$errors = [];
			if ( empty( $post_meta['bingo_card_type'][0] ) ) {
				$errors[] = "Bingo card type isn't defined.";
			}
			if ( empty( $post_meta['bingo_grid_size'][0] ) ) {
				$errors[] = "Bingo card grid size isn't defined.";
			}
			// Setting errors to show
			if ( ! empty( $errors ) ) {
				foreach ( $errors as $key => $error ) {
					$unique = 'bc_error_' . ( $key + 1 );
					add_settings_error( $unique, str_replace( '_', '-', $unique ), $error );
					settings_errors( $unique );
				}
			}
		}

		return $messages;
	}

	/**
	 * Enqueue admin scripts and styles
	 */
	public function enqueue_admin_script_and_styles() {
		global $post_type;
		if ( $post_type === 'bingo_theme' || $post_type === 'bingo_card' ) {
			wp_enqueue_script( 'lbcg-vanilla-js', $this->attributes['includes_url'] . 'js/vanilla.js' );
			wp_enqueue_script( 'html2canvas-js', $this->attributes['admin_url'] . 'js/html2canvas.min.js', [], $this->attributes['plugin_version'] );
			if ( ! did_action( 'wp_enqueue_media' ) ) {
				wp_enqueue_media();
			}
			wp_enqueue_script( 'lbcg-admin-js', $this->attributes['admin_url'] . 'js/lbcg-admin.js', [], $this->attributes['plugin_version'] );
			wp_enqueue_script( 'lbcg-public-js', $this->attributes['public_url'] . 'js/lbcg-public.js', [], $this->attributes['plugin_version'] );
			wp_localize_script( 'lbcg-public-js', 'LBCG', [
				'fonts'          => LBCG_Helper::$fonts,
				'freeSquareWord' => LBCG_Helper::$free_space_word,
				'ajaxUrl'        => admin_url( 'admin-ajax.php' )
			] );
			wp_enqueue_style( 'lbcg-admin-css', $this->attributes['admin_url'] . 'css/lbcg-admin.css', [], $this->attributes['plugin_version'] );
			wp_enqueue_style( 'lbcg-public-css', $this->attributes['public_url'] . 'css/lbcg-public.min.css', [], $this->attributes['plugin_version'] );
		}
	}

	/**
	 * Disable emoji prints
	 *
	 * @return void
	 */
	public function disable_emojy_print() {
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
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
			$data = get_post_meta( $post->ID );
			// Load attributes
			if ( ! empty( $data['grid_square'] ) ) {
				$grid_square = unserialize( $data['grid_square'][0] );
			} else {
				$grid_square = [
					'font_color' => '#ffffff',
					'color'      => '#000'
				];
			}
			if ( ! empty( $data['bc_header'][0] ) ) {
				$bc_header = unserialize( $data['bc_header'][0] );
			} else {
				$bc_header = [
					'font_color' => '#ffffff',
					'color'      => '#d6be89',
					'image'      => '',
					'opacity'    => 100,
					'repeat'     => 'no-repeat',
					'bg_pos'     => 'center center',
					'bg_size'    => 'cover'
				];
			}
			if ( ! empty( $data['bc_grid'][0] ) ) {
				$bc_grid = unserialize( $data['bc_grid'][0] );
			} else {
				$bc_grid = [
					'font_color'   => '#000',
					'border_color' => '#000',
					'color'        => '#997d3c',
					'image'        => '',
					'opacity'      => 100,
					'repeat'       => 'no-repeat',
					'bg_pos'       => 'center center',
					'bg_size'      => 'cover'
				];
			}
			if ( ! empty( $data['bc_card'][0] ) ) {
				$bc_card = unserialize( $data['bc_card'][0] );
			} else {
				$bc_card = [
					'color'   => '#d6be89',
					'image'   => '',
					'opacity' => 100,
					'repeat'  => 'no-repeat',
					'bg_pos'  => 'center center',
					'bg_size' => 'cover'
				];
			}
			include_once $this->attributes['public_templates_path'] . '/lbcg-public-properties.php';
			// Load custom css
			if ( ! empty( $data['bingo_card_custom_css'][0] ) ) {
				?>
                <style type="text/css">
                    <?php echo trim( wp_strip_all_tags( $data['bingo_card_custom_css'][0] ) ); ?>
                </style>
				<?php
			}
		}
	}
}