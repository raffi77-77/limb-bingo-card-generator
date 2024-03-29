<?php

/**
 * The ajax related functionality of the plugin
 */
class LBCG_Ajax {
	/**
	 * Plugin all needed properties in one place
	 *
	 * @var array $attributes The array containing main attributes of the plugin
	 */
	protected $attributes = [];

	/**
	 * Construct Bingo Card Ajax object
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
		add_action( 'wp_ajax_nopriv_lbcg_bc_generation', array( $this, 'generation' ) );
		add_action( 'wp_ajax_lbcg_bc_generation', array( $this, 'generation' ) );
		add_action( 'wp_ajax_nopriv_lbcg_bc_invitation', array( $this, 'invitation' ) );
		add_action( 'wp_ajax_lbcg_bc_invitation', array( $this, 'invitation' ) );
		add_action( 'wp_ajax_nopriv_lbcg_get_card_content', array( $this, 'get_card_content' ) );
		add_action( 'wp_ajax_lbcg_get_card_content', array( $this, 'get_card_content' ) );
		add_action( 'created_ubud-category', array( $this, 'save_taxonomy_custom_fields' ) );
	}

	/**
	 * Generate author card
	 */
	public function generation() {
		// Check current bingo theme
		$post_type = get_post_type( $_POST['bingo_theme_id'] );
		if ( empty( $post_type ) || $post_type !== 'bingo_theme' ) {
			print_r( json_encode( [
				'success'    => false,
				'errors'     => [ "Invalid request" ],
				'redirectTo' => get_site_url()
			] ) );
			die();
		}
		// Collect card data
		$result = LBCG_Helper::collect_card_data_from( $_POST );
		if ( $result['success'] === false ) {
			print_r( json_encode( [
				'success'    => false,
				'errors'     => $result['data'],
				'redirectTo' => ''
			] ) );
			die();
		}
		// Create card
		$bc_result = LBCG_Helper::insert_bingo_card( $result['data'], 'publish' );
		if ( $bc_result === false ) {
			print_r( json_encode( [
				'success'    => false,
				'errors'     => [ "Failed to save data. Please try again." ],
				'redirectTo' => ''
			] ) );
			die();
		}
		// Save card data
		LBCG_Helper::save_bingo_meta_fields( $bc_result['id'], $result['data'], $_POST['bingo_theme_id'] );
		update_post_meta( $bc_result['id'], 'bingo_theme_id', $_POST['bingo_theme_id'] );
        // Save thumbnail
        if ( ! empty( $_POST['bc_thumbnail'] ) ) {
	        $thumb_name = sanitize_file_name( $bc_result['title'] ) . '-' . wp_generate_password( 12, false );
	        LBCG_Helper::set_as_featured_image( $_POST['bc_thumbnail'], $bc_result['id'], $thumb_name . '.webp' );
        }
		print_r( json_encode( [
			'success'    => true,
			'errors'     => [],
			'redirectTo' => get_permalink( $_POST['bingo_theme_id'] ) . 'invitation/?bc=' . $bc_result['uniq_id']
		] ) );
		die();
	}

	/**
	 * Create all cards and invite them
	 */
	public function invitation() {
		try {
			// Check current parent bingo card
			if ( empty( $_POST['bingo_card_uid'] ) ) {
				print_r( json_encode( [
					'success'       => false,
					'errors'        => [ "Invalid request." ],
					'failedInvites' => [],
					'redirectTo'    => ''
				] ) );
				die();
			}
			$bc_posts = get_posts( [
				'name'           => $_POST['bingo_card_uid'],
				'post_type'      => 'bingo_card',
				'posts_per_page' => 1,
				'post_status'    => 'publish'
			] );
			if ( empty( $bc_posts[0]->ID ) ) {
				print_r( json_encode( [
					'success'       => false,
					'errors'        => [ "Invalid request." ],
					'failedInvites' => [],
					'redirectTo'    => ''
				] ) );
				die();
			}
			$bingo_card = $bc_posts[0];
            // Get thumbnail
			if ( ! empty( $_POST['bingo_card_thumb'] ) ) {
				$thumbnail_base64 = $_POST['bingo_card_thumb'];
			} else {
				$thumbnail_base64 = '';
			}
			// Set card thumbnail
			if ( ! empty( $thumbnail_base64 ) ) {
	            $thumb_name = sanitize_file_name( $bingo_card->post_title ) . '-' . wp_generate_password( 12, false );
	            LBCG_Helper::set_as_featured_image( $thumbnail_base64, $bingo_card->ID, $thumb_name . '.webp' );
            }
			// Get emails
			$author_email  = trim( $_POST['author_email'] );
			$invite_emails = preg_split( '/\r\n|\r|\n/', $_POST['invite_emails'] );
			$invite_emails = array_map( 'trim', $invite_emails );
			$invite_emails = array_unique( $invite_emails );
			// Remove author email from invite emails list
			if ( $key = array_search( $author_email, $invite_emails ) !== false ) {
				unset( $invite_emails[ $key ] );
			}
			// Check email validations
			if ( ! LBCG_Helper::is_valid_emails( $author_email ) ) {
				$error_messages[] = "Your email is not valid. Please enter correct email.";
			}
			if ( ! LBCG_Helper::is_valid_emails( $invite_emails ) ) {
				$error_messages[] = "Please check invitation emails validation and try again.";
			}
			if ( ! empty( $error_messages ) ) {
				print_r( json_encode( [
					'success'       => false,
					'errors'        => $error_messages,
					'failedInvites' => [],
					'redirectTo'    => ''
				] ) );
				die();
			}
			// Save user email for 90 days
			if ( isset( $_COOKIE['LBCG_IUE'] ) && $_COOKIE['LBCG_IUE'] !== $author_email || ! isset( $_COOKIE['LBCG_IUE'] ) ) {
				setcookie( 'LBCG_IUE', $author_email, strtotime( '+90 days' ), '/bingo-cards/', $_SERVER['HTTP_HOST'] );
			}
			// Get privacy message
			if ( isset( $_POST['author_message'] ) ) {
				$author_message = trim( strip_tags( $_POST['author_message'], [ 'p', 'a', 'b', 'string', 'i', 'em', 'mark', 'small', 'del', 'ins', 'sub', 'sup' ] ) );
			} else {
				$author_message = '';
			}
			// Create bingo cards and invite
			$result = LBCG_Helper::invite_emails( $bingo_card->ID, $thumbnail_base64, $author_email, $invite_emails, $author_message );
			if ( $result['success'] === false ) {
				print_r( json_encode( [
					'success'       => false,
					'errors'        => $result['errors'],
					'failedInvites' => $result['failed_invites'],
					'redirectTo'    => get_permalink( $bingo_card->ID ) . 'all'
				] ) );
				die();
			}
			print_r( json_encode( [
				'success'       => true,
				'errors'        => [],
				'failedInvites' => $result['failed_invites'],
				'redirectTo'    => get_permalink( $bingo_card->ID ) . 'all'
			] ) );
			die();
		}
		catch ( \Exception $e ) {
			print_r( json_encode( [
				'success'       => false,
				'errors'        => [ "Something went wrong." ],
				'failedInvites' => [],
				'redirectTo'    => ''
			] ) );
			die();
		}
	}

	/**
	 * Save taxonomy image id
	 *
	 * @param   int  $term_id
	 *
	 * @return void
	 */
	public function save_taxonomy_custom_fields( $term_id ) {
		update_term_meta( $term_id, '_lbcg_created_at', time() );
		$admin_instance = LBCG_Admin::get_instance( $this->attributes );
		$admin_instance->save_taxonomy_custom_fields( $term_id );
	}

	/**
	 * Get card content html
	 *
	 * @return void
	 */
	public function get_card_content() {
		$type                 = $_POST['card_type'];
		$title                = $_POST['card_title'];
		$spec_title           = ! empty( $_POST['spec_title'] ) ? str_split( $_POST['spec_title'] ) : [ 'B', 'I', 'N', 'G', 'O' ];
		$additional_spec_part = '';
		switch ( count( $spec_title ) ) {
			case 4:
			case 3:
				$additional_spec_part = '<span></span>';
				break;
			case 2:
			case 1:
				$additional_spec_part = '<span></span><span></span>';
				break;
		}
		ob_start();
		?>
        <div class="lbcg-card-header-holder">
            <div class="lbcg-card-header">
                <span class="lbcg-card-header-text"><?php echo $title; ?></span>
            </div>
            <div class="lbcg-card-subtitle" style="<?php echo $type !== '1-75' ? 'display: none;' : ''; ?>">
                <span class="lbcg-card-subtitle-text"><?php
                    echo $additional_spec_part;
                    echo ! empty( $spec_title ) ? '<span>' . implode( '</span><span>', $spec_title ) . '</span>' : '';
                    echo $additional_spec_part; ?></span>
            </div>
        </div>
        <div class="lbcg-card-body">
			<?php
			if ( $type === '1-90' ) {
				$bingo_card_words = LBCG_Helper::get_1_90_bingo_card_numbers();
				foreach ( $bingo_card_words as $single_card_words ) { ?>
                    <div class="lbcg-card-body-grid lbcg-grid-9">
						<?php
						foreach ( $single_card_words as $number ) { ?>
                            <div class="lbcg-card-col">
                                <span class="lbcg-card-text"><?php echo $number; ?></span>
                            </div>
						<?php } ?>
                    </div>
					<?php
				}
			} else {
				if ( $type === '1-75' ) {
					$size             = '5x5';
					$bingo_card_words = LBCG_Helper::get_1_75_bingo_card_numbers();
				} else {
					$size = $_POST['card_grid_size'];
					if ( ! empty( $_POST['card_content'] ) ) {
						$bingo_card_content = $_POST['card_content'];
					} else {
						$result             = LBCG_Helper::get_bg_default_content( $size );
						$bingo_card_content = $result['words'];
					}
					if ( strpos( $bingo_card_content, "\r\n" ) !== false ) {
						$bingo_card_words = explode( "\r\n", $bingo_card_content );
					} else {
						$bingo_card_words = explode( "\n", $bingo_card_content );
					}
				}
				$bingo_grid_free_square = $_POST['free_square'] === 'true' ? true : false;
				?>
                <div class="lbcg-card-body-grid lbcg-grid-<?php echo $size[0]; ?>">
					<?php
					$grid_sq_count = $size ** 2;
					for ( $i = 1; $i <= $grid_sq_count; $i ++ ):
						if ( (int) ceil( $grid_sq_count / 2 ) === $i && $bingo_grid_free_square ) {
							$is_free_space = true;
						} else {
							$is_free_space = false;
						} ?>
                        <div class="lbcg-card-col<?php echo $is_free_space ? ' lbcg-free-space' : ''; ?>">
                            <span class="lbcg-card-text"><?php
                                if ( $is_free_space ) {
                                    echo LBCG_Helper::$free_space_word;
                                } else {
                                    echo $bingo_card_words[ $i - 1 ];
                                }
                                ?></span>
                        </div>
					<?php endfor; ?>
                </div>
				<?php
			}
			?>
        </div>
		<?php
		echo ob_get_clean();
		die();
	}
}