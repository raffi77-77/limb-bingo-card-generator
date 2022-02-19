<?php

/**
 * The admin-specific functionality of the plugin
 */
class LBCG_Admin {
	/**
	 * An instance of this class
	 *
	 * @var LBCG_Admin|null $instance An object of this class
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
	 * Get instance
	 *
	 * @param $attributes
	 *
	 * @return LBCG_Admin|null
	 */
	public static function get_instance( $attributes = [] ) {
		if ( null === self::$instance && ! empty( $attributes ) ) {
			// Create new instance
			self::$instance = new LBCG_Admin( $attributes );
		}

		return self::$instance;
	}

	/**
	 * Construct Bingo Card Admin object
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
		add_action( 'add_meta_boxes', array( $this, 'add_custom_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_custom_fields' ), 10, 3 );
		add_action( 'post_updated_messages', array( $this, 'show_editor_message' ) );
		add_action( 'ubud-category_add_form_fields', array( $this, 'add_taxonomy_custom_content' ) );
		add_action( 'ubud-category_edit_form_fields', array( $this, 'edit_taxonomy_custom_content' ), 10, 2 );
		add_action( 'edited_ubud-category', array( $this, 'save_taxonomy_custom_fields' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_script_and_styles' ), 11 );
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
		if ( ( is_singular( [ 'bingo_theme', 'bingo_card' ] ) ) && get_post_status() !== 'auto-draft' ) {
			$post_meta = get_post_meta( get_the_ID() );
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
	 * Add taxonomy custom content
	 *
	 * @param   string  $term
	 *
	 * @return void
	 */
	public function add_taxonomy_custom_content( $term ) {
		?>
        <div class="form-field form-required term-group">
            <label for="lbcg-uc-image-set">Set featured image</label>
            <input type="hidden" id="lbcg-uc-image" name="lbcg_uc_image" value="">
            <input type="button" id="lbcg-uc-image-set" class="button" value="Choose featured image">
            <input type="button" id="lbcg-uc-image-remove" class="button" value="Remove image">
            <img id="lbcg-uc-img" src="" alt="" width="50px" height="50px" style="display: none;">
        </div>
		<?php
		ob_start();
		wp_editor( '', 'lbcg-uc-intro-text', [
			'wpautop'       => false,
			'media_buttons' => false,
			'textarea_name' => 'lbcg_uc_intro_text',
			'textarea_cols' => 40,
			'textarea_rows' => 10
		] );
		$editor = ob_get_clean();
		?>
        <div class="form-field">
            <label for="lbcg-uc-intro-text">Intro text</label>
			<?php echo $editor; ?>
            <p>The intro html text.</p>
        </div>
		<?php
	}

	/**
	 * Edit taxonomy custom content
	 *
	 * @param   WP_Term  $term
	 * @param   string   $taxonomy
	 *
	 * @return void
	 */
	public function edit_taxonomy_custom_content( $term, $taxonomy ) {
		$thumnail_id  = get_term_meta( $term->term_id, '_lbcg_thumbnail_id', true );
		$thumnail_url = wp_get_attachment_image_url( $thumnail_id );
		?>
        <tr class="form-field">
            <th scope="row"><label for="lbcg-uc-image-set">Set featured image</label></th>
            <td>
                <input type="hidden" id="lbcg-uc-image" name="lbcg_uc_image" value="<?php echo $thumnail_id ?>">
                <input type="button" id="lbcg-uc-image-set" class="button" value="Choose featured image">
                <input type="button" id="lbcg-uc-image-remove" class="button" value="Remove image">
                <img id="lbcg-uc-img" src="<?php echo $thumnail_url; ?>" alt="" width="50px" height="50px" <?php echo ! $thumnail_url ? 'style="display: none;"' : ''; ?>>
                <p class="description">Set featured image for the taxonomy.</p>
            </td>
        </tr>
		<?php
		ob_start();
		wp_editor( get_term_meta( $term->term_id, 'lbcg_intro_text', true ), 'lbcg-uc-intro-text', [
			'wpautop'       => false,
			'media_buttons' => false,
			'textarea_name' => 'lbcg_uc_intro_text',
			'textarea_cols' => 40,
			'textarea_rows' => 10
		] );
		$editor = ob_get_clean();
		?>
        <tr class="form-field">
            <th scope="row"><label for="lbcg-uc-intro-text">Intro text</label></th>
            <td>
				<?php echo $editor; ?>
                <p class="description">The intro html text.</p>
            </td>
        </tr>
		<?php
	}

	/**
	 * Save taxonomy custom fields
	 *
	 * @param   int  $term_id
	 *
	 * @return void
	 */
	public function save_taxonomy_custom_fields( $term_id ) {
		if ( isset( $_POST['lbcg_uc_image'] ) ) {
			update_term_meta( $term_id, '_lbcg_thumbnail_id', is_numeric( $_POST['lbcg_uc_image'] ) ? (int) $_POST['lbcg_uc_image'] : 0 );
		}
		if ( isset( $_POST['lbcg_uc_intro_text'] ) ) {
			update_term_meta( $term_id, 'lbcg_intro_text', trim( $_POST['lbcg_uc_intro_text'] ) );
		}
        if ( empty( get_term_meta( $term_id, '_lbcg_created_at', true ) ) ) {
            update_term_meta( $term_id, '_lbcg_created_at', time() );
        }
	}

	/**
	 * Enqueue admin scripts and styles
	 */
	public function enqueue_admin_script_and_styles() {
		$post_type = get_post_type();
		if ( $post_type === 'bingo_theme' || $post_type === 'bingo_card' ) {
			wp_enqueue_script( 'lbcg-vanilla-js', $this->attributes['includes_url'] . 'js/vanilla.js' );
			wp_enqueue_script( 'html2canvas-js', $this->attributes['includes_url'] . 'js/html2canvas.min.js', [], $this->attributes['plugin_version'] );
			if ( ! did_action( 'wp_enqueue_media' ) ) {
				wp_enqueue_media();
			}
			wp_enqueue_script( 'lbcg-admin-js', $this->attributes['admin_url'] . 'js/lbcg-admin.min.js', [], $this->attributes['plugin_version'] );
			wp_enqueue_script( 'lbcg-public-js', $this->attributes['public_url'] . 'js/lbcg-public.min.js', [], $this->attributes['plugin_version'] );
			wp_localize_script( 'lbcg-public-js', 'LBCG', [
				'fonts'          => LBCG_Helper::$fonts,
				'freeSquareWord' => LBCG_Helper::$free_space_word,
				'ajaxUrl'        => admin_url( 'admin-ajax.php' )
			] );
			wp_enqueue_style( 'lbcg-admin-css', $this->attributes['admin_url'] . 'css/lbcg-admin.css', [], $this->attributes['plugin_version'] );
			wp_enqueue_style( 'lbcg-public-css', $this->attributes['public_url'] . 'css/lbcg-public.min.css', [], $this->attributes['plugin_version'] );
		} else {
			global $taxnow;
			if ( $taxnow === 'ubud-category' ) {
				if ( ! did_action( 'wp_enqueue_media' ) ) {
					wp_enqueue_media();
				}
				wp_enqueue_script( 'lbcg-admin-js', $this->attributes['admin_url'] . 'js/lbcg-admin.min.js', [], $this->attributes['plugin_version'] );
			}
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
	 * Get current post metadata
	 *
	 * @return array|null
	 */
	public function get_post_data() {
		return $this->data;
	}

	/**
	 * Add custom css
	 */
	public function add_custom_css() {
		$post_type = get_post_type();
		if ( $post_type === 'bingo_theme' || $post_type === 'bingo_card' ) {
			// Load google fonts
			?>
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
			<?php foreach ( LBCG_Helper::$fonts as $font ): ?>
                <link href="<?php echo $font['url'] ?>" rel="stylesheet">
			<?php endforeach;
			$this->data = get_post_meta( get_the_ID() );
			// Load attributes
			if ( ! empty( $this->data['grid_square'] ) ) {
				$grid_square = unserialize( $this->data['grid_square'][0] );
			} else {
				$grid_square = [
					'font_color' => '#ffffff',
					'color'      => '#000'
				];
			}
			if ( ! empty( $this->data['bc_header'][0] ) ) {
				$bc_header = unserialize( $this->data['bc_header'][0] );
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
			if ( ! empty( $this->data['bc_grid'][0] ) ) {
				$bc_grid = unserialize( $this->data['bc_grid'][0] );
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
			if ( ! empty( $this->data['bc_card'][0] ) ) {
				$bc_card = unserialize( $this->data['bc_card'][0] );
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
			$data = $this->data;
			include_once $this->attributes['public_templates_path'] . '/lbcg-public-properties.php';
			// Load custom css
			if ( ! empty( $this->data['bingo_card_custom_css'][0] ) ) {
				?>
                <style type="text/css">
                    <?php echo trim( wp_strip_all_tags( $this->data['bingo_card_custom_css'][0] ) ); ?>
                </style>
				<?php
			}
		}
	}
}