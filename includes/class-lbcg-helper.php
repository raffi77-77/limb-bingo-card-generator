<?php

/**
 * The class the helps with different static methods
 */
class LBCG_Helper {
	/**
	 * Google Fonts
	 *
	 * @var string[]
	 */
	public static $fonts
		= [
			'mochiy-pop-p-one' => [
				'name' => 'Mochiy Pop P One',
				'url'  => 'https://fonts.googleapis.com/css2?family=Mochiy+Pop+P+One&display=swap'
			],
			'dancing-script'   => [
				'name' => 'Dancing Script',
				'url'  => 'https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap'
			],
			'saira-condensed'  => [
				'name' => 'Saira Condensed',
				'url'  => 'https://fonts.googleapis.com/css2?family=Saira+Condensed:wght@100&display=swap'
			],
			'creepster'        => [
				'name' => 'Creepster',
				'url'  => 'https://fonts.googleapis.com/css2?family=Creepster&display=swap'
			],
			'holtwood-one-sc'  => [
				'name' => 'Holtwood One SC',
				'url'  => 'https://fonts.googleapis.com/css2?family=Holtwood+One+SC&display=swap'
			],
			'sofadi-one'       => [
				'name' => 'Sofadi One',
				'url'  => 'https://fonts.googleapis.com/css2?family=Sofadi+One&display=swap'
			]
		];

	/**
	 * Free space word
	 *
	 * @var string
	 */
	public static $free_space_word = '&#9733;';

	/**
     * Cards global font size
     *
	 * @var int
	 */
	public static $font_size = 16;

	/**
	 * Register custom post types and hooks
	 */
	public static function register_custom_post_types() {
		self::register_bingo_theme_post_type();
		self::register_bingo_card_post_type();
//		add_filter( 'post_type_link', array( 'LBCG_Helper', 'check_post_link' ), 10, 2 );
	}

	/**
	 * Register custom post types for Bingo Theme
	 */
	public static function register_bingo_theme_post_type() {
		// Custom taxonomy settings
		$labels = array(
			'name'              => __( 'UBUD Categories', 'textdomain' ),
			'singular_name'     => __( 'UBUD Category', 'textdomain' ),
			'search_items'      => __( 'Search UBUD Categories', 'textdomain' ),
			'all_items'         => __( 'All UBUD Categories', 'textdomain' ),
			'parent_item'       => __( 'Parent UBUD Category', 'textdomain' ),
			'parent_item_colon' => __( 'Parent UBUD Category:', 'textdomain' ),
			'edit_item'         => __( 'Edit UBUD Category', 'textdomain' ),
			'update_item'       => __( 'Update UBUD Category', 'textdomain' ),
			'add_new_item'      => __( 'Add New UBUD Category', 'textdomain' ),
			'new_item_name'     => __( 'New UBUD Category Name', 'textdomain' ),
			'menu_name'         => __( 'UBUD Categories', 'textdomain' ),
		);
		// Register bingo_theme custom taxonomy
		register_taxonomy( 'ubud-category', array( 'bingo_theme' ), array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'bingo-cards/category' ),
		) );
		add_rewrite_rule( 'bingo-cards/category/([^/]+)/?$', 'index.php?taxonomy=ubud-category&post_type=bingo_theme&ubud-category=$matches[0]', 'top' );
		// Custom post type settings
		$labels = array(
			'name'           => __( 'UBUD Bingo themes', 'textdomain' ),
			'singular_name'  => __( 'UBUD Bingo theme', 'textdomain' ),
			'menu_name'      => __( 'UBUD Bingo themes', 'textdomain' ),
			'name_admin_bar' => __( 'UBUD Bingo theme', 'textdomain' ),
			'add_new'        => __( 'Add new', 'textdomain' ),
			'add_new_item'   => __( 'Add new UBUD Bingo theme', 'textdomain' ),
			'new_item'       => __( 'New UBUD Bingo theme', 'textdomain' ),
			'edit_item'      => __( 'Edit UBUD Bingo theme', 'textdomain' ),
			'view_item'      => __( 'View UBUD Bingo theme', 'textdomain' ),
			'all_items'      => __( 'All UBUD Bingo themes', 'textdomain' ),
			'search_items'   => __( 'Search UBUD Bingo themes', 'textdomain' ),
			'not_found'      => __( 'No UBUD Bingo Themes found.', 'textdomain' )
		);
		// Register bingo_card custom post type
		register_post_type( 'bingo_theme', array(
			'labels'             => $labels,
			'description'        => 'UBUD Bingo Theme',
			'public'             => true,
			'publicly_queryable' => true,
			'query_var'          => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => true,
			'rewrite'            => array( 'slug' => 'bingo-cards' ),
			'supports'           => array( 'title', 'editor', 'author' ),
			'taxonomies'         => array( 'ubud-category' ),
		) );
		add_rewrite_rule( 'bingo-cards/([^/]+)/?(([^/]+)/?)?$', 'index.php?post_type=bingo_theme&name=$matches[1]', 'top' );
	}

	/**
	 * Register custom post type for Bingo Card
	 */
	public static function register_bingo_card_post_type() {
		// Custom post type settings
		$labels   = array(
			'name'           => __( 'UBUD Bingo Cards', 'textdomain' ),
			'singular_name'  => __( 'UBUD Bingo Card', 'textdomain' ),
			'menu_name'      => __( 'UBUD Bingo Cards', 'textdomain' ),
			'name_admin_bar' => __( 'UBUD Bingo Card', 'textdomain' ),
			'add_new'        => __( 'Add new', 'textdomain' ),
			'add_new_item'   => __( 'Add new UBUD Bingo Card', 'textdomain' ),
			'new_item'       => __( 'New UBUD Bingo Card', 'textdomain' ),
			'edit_item'      => __( 'Edit UBUD Bingo Card', 'textdomain' ),
			'view_item'      => __( 'View UBUD Bingo Card', 'textdomain' ),
			'all_items'      => __( 'All UBUD Bingo Cards', 'textdomain' ),
			'search_items'   => __( 'Search UBUD Bingo Cards', 'textdomain' ),
			'not_found'      => __( 'No UBUD Bingo Cards found.', 'textdomain' )
		);
		$supports = array( 'title', 'editor', 'author' );
		// Register bingo_card custom post type
		register_post_type( 'bingo_card', array(
			'labels'             => $labels,
			'description'        => 'UBUD Bingo Card',
			'public'             => true,
			'publicly_queryable' => true,
			'query_var'          => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'rewrite'            => array( 'slug' => 'ubud-bingo-card' ),
			'supports'           => $supports,
		) );
		add_rewrite_rule( 'ubud-bingo-card/([^/]+)/?(([^/]+)/?)?$', 'index.php?post_type=bingo_card&name=$matches[1]', 'top' );
	}

	/**
	 * Check post link
	 *
	 * @param   string           $post_link
	 * @param   null|int|object  $post
	 *
	 * @return string
	 */
	public static function check_post_link( $post_link, $post = null ) {
		$post = get_post( $post );
		if ( $post instanceof WP_Post && $post->post_type === 'bingo_theme' ) {
			$terms = wp_get_object_terms( $post->ID, 'ubud-category' );
			if ( $terms ) {
				foreach ( $terms as $term ) {
					if ( 0 === $term->parent ) {
						return str_replace( '%ubud-category%', $term->slug, $post_link );
					}
				}
			} else {
				return str_replace( '%ubud-category%', 'uncategorized', $post_link );
			}
		}

		return $post_link;
	}

	/**
	 * Checks weather the request comes from admin|ajax|cron|public
	 *
	 * @param   null|string  $type
	 *
	 * @return bool
	 */
	public static function is_request( $type = null ) {
		$is_ajax = ( defined( 'DOING_AJAX' ) && DOING_AJAX );
		switch ( $type ) {
			case 'admin' :
				return is_admin() && ! $is_ajax;
			case 'ajax' :
				return $is_ajax;
			case 'cron' :
				return ( defined( 'DOING_CRON' ) && DOING_CRON );
			case 'public' :
				return ( ! is_admin() && ! $is_ajax );
		}

		return false;
	}

	/**
	 * Get bingo card default content
	 *
	 * @param   string  $size
	 *
	 * @return array
	 */
	public static function get_bg_default_content( $size ) {
		$min_count = 25;
		if ( $size === '3x3' ) {
			$min_count = 9;
		} elseif ( $size === '4x4' ) {
			$min_count = 16;
		}

		return [
			'words_count' => $min_count,
			'words'       => implode( "\n", range( 1, $min_count ) )
		];
	}

	/**
	 * Get 1-75 bingo card numbers
	 *
	 * @param   bool  $random
	 *
	 * @return array
	 */
	public static function get_1_75_bingo_card_numbers( $random = false ) {
		$num_cols = [];
		for ( $i = 1; $i < 62; $i += 15 ) {
			$temp_numbers = range( $i, $i + 14 );
			if ( $random === true ) {
				shuffle( $temp_numbers );
			}
			$num_cols[] = $temp_numbers;
		}
		$bingo_card_numbers = [];
		$i                  = 0;
		$j                  = 0;
		while ( $j < 5 ) {
			$bingo_card_numbers[] = $num_cols[ $i ][ $j ];
			++ $i;
			if ( ( $i %= 5 ) === 0 ) {
				++ $j;
			}
		}

		return $bingo_card_numbers;
	}

	/**
	 * Get 1-90 bingo card numbers
	 *
	 * @param   bool  $random
	 *
	 * @return array
	 */
	public static function get_1_90_bingo_card_numbers( $random = false ) {
		if ( ! $random ) {
			// Return card default numbers
			return [
				[ 6, 17, '', 35, 44, 53, '', '', '', 3, '', 28, '', '', 54, 65, '', 81, '', 10, 25, 30, '', '', '', 73, 83 ],
				[ 4, '', '', '', '', 57, 61, 71, 82, 1, '', '', 38, '', 56, '', 75, 80, '', 15, 24, '', 47, '', 68, 70, '' ],
				[ '', 12, 29, '', 46, 59, 66, '', '', '', '', 20, 32, 40, '', '', 79, 85, 2, 14, 26, '', 49, 55, '', '', '' ],
				[ '', 13, '', 33, '', 52, 63, '', 90, 7, '', '', 39, '', '', 64, 76, 86, '', 19, 22, 34, 42, '', '', 74, '' ],
				[ '', 16, 23, 31, '', 50, '', '', 87, 9, 11, 21, '', 41, '', '', '', 84, 8, '', '', '', 43, '', 62, 77, 89 ],
				[ '', '', '', 37, 45, 51, 67, 78, '', 5, '', 27, '', 48, 58, 60, '', '', '', 18, '', 36, '', '', 69, 72, 88 ]
			];
		}
		$sub_cards             = array_fill( 0, 18, array_fill( 0, 9, 0 ) );
		$num_counts_in_columns = [ 9, 10, 10, 10, 10, 10, 10, 10, 11 ];
		// Set numbers indexes
		for ( $i = 0; $i < 18; $i ++ ) {
			$possibilities = [];
			$forced        = [];
			$needs_count   = 5;
			// Get possibility and forced indexes
			for ( $c = 0; $c < 9; $c ++ ) {
				if ( $num_counts_in_columns[ $c ] === 18 - $i ) {
					$forced[] = $c;
					$needs_count --;
				} elseif ( $num_counts_in_columns[ $c ] ) {
					$possibilities[] = $c;
				}
			}
			shuffle( $possibilities );
			// Set possibilities indexes
			for ( $j = 0; $j < $needs_count; $j ++ ) {
				-- $num_counts_in_columns[ $possibilities[ $j ] ];
				++ $sub_cards[ $i ][ $possibilities[ $j ] ];
			}
			// Set forced indexes
			for ( $j = 0; $j < 5 - $needs_count; $j ++ ) {
				-- $num_counts_in_columns[ $forced[ $j ] ];
				++ $sub_cards[ $i ][ $forced[ $j ] ];
			}
		}
		$numbers = range( 1, 90 );
		shuffle( $numbers );
		$line_in_column = array_fill( 0, 9, 0 );
		// Fill with numbers
		for ( $i = 0; $i < 90; $i ++ ) {
			if ( $numbers[ $i ] === 90 ) {
				$column = 8;
			} else {
				$column = (int) ( $numbers[ $i ] / 10 );
			}
			while ( $sub_cards[ $line_in_column[ $column ] ][ $column ] === 0 ) {
				++ $line_in_column[ $column ];
			}
			$sub_cards[ $line_in_column[ $column ] ][ $column ] = $numbers[ $i ];
			++ $line_in_column[ $column ];
		}
		// Random move all rows
		shuffle( $sub_cards );
		// Collect card items
		$bingo_card_numbers = [];
		for ( $i = 0; $i < 18; $i ++ ) {
			for ( $j = 0; $j < 9; $j ++ ) {
				$bingo_card_numbers[ (int) ( $i / 3 ) ][] = $sub_cards[ $i ][ $j ] ?: '';
			}
		}

		return $bingo_card_numbers;
	}

	/**
	 * Get card meta data
	 *
	 * @param   array  $data
	 * @param   bool   $from_meta
	 *
	 * @return array
	 */
	public static function collect_card_data_from( $data, $from_meta = false ) {
		$errors = [];
		// Check some cases
		if ( empty( $data['bingo_card_type'] ) || ( $from_meta && empty( $data['bingo_card_type'][0] ) ) ) {
			$errors[] = "Bingo card type isn't defined.";
		}
		if ( empty( $data['bingo_grid_size'] ) || ( $from_meta && empty( $data['bingo_grid_size'][0] ) ) ) {
			$errors[] = "Bingo card grid size isn't defined.";
		}
		if ( ( $data['bingo_card_type'] !== '1-75' && $data['bingo_card_type'] !== '1-90' && empty( $data['bingo_card_content'] ) ) ) {
			if ( $from_meta && ( $data['bingo_card_type'][0] !== '1-75' && $data['bingo_card_type'][0] !== '1-90' && empty( $data['bingo_card_content'][0] ) ) ) {
				$errors[] = "Bingo card words/emojis or numbers are empty.";
			}
		}
		if ( ( empty( $data['bc_header'] ) || empty( $data['bc_grid'] ) || empty( $data['bc_card'] ) ) ) {
			if ( $from_meta && ( empty( $data['bc_header'][0] ) || empty( $data['bc_grid'][0] ) || empty( $data['bc_card'][0] ) ) ) {
				$errors[] = "Bingo card styles are not defined.";
			}
		}
		if ( ! empty( $errors ) ) {
			return [
				'success' => false,
				'data'    => $errors
			];
		}
		// Collect data
		$card_data = [
			'bingo_card_type'        => '',
			'bingo_grid_size'        => '',
			'bingo_card_title'       => '',
			'lbcg_font_size'         => '',
			'bingo_card_spec_title'  => '',
			'bingo_card_content'     => '',
			'grid_square'            => '',
			'bc_header'              => '',
			'bc_grid'                => '',
			'bc_card'                => '',
			'bingo_card_font'        => '',
			'bingo_card_wrap_words'  => '',
			'bingo_card_free_square' => '',
			'bingo_card_custom_css'  => ''
		];
		foreach ( $card_data as $key => $value ) {
			if ( ! empty( $data[ $key ] ) || ( $from_meta && ! empty( $data[ $key ][0] ) ) ) {
				$card_data[ $key ] = $from_meta ? maybe_unserialize( $data[ $key ][0] ) : $data[ $key ];
			}
		}

		return [
			'success' => true,
			'data'    => $card_data
		];
	}

	/**
	 * Save bingo card/theme meta fields
	 *
	 * @param   int       $post_id
	 * @param   array     $data
	 * @param   null|int  $theme_id
	 */
	public static function save_bingo_meta_fields( $post_id, $data, $theme_id = null ) {
		if ( $theme_id !== null ) {
			$theme_meta_data = get_post_meta( $theme_id );
		}
		$special_cards = array( '1-75', '1-90' );
		$post_type     = get_post_type( $post_id );
		// Custom intro text for generation page
		if ( $post_type === 'bingo_theme' ) {
			update_post_meta( $post_id, 'bt_intro_text', $data['bt_intro_text'] );
		}
		// Type, grid and font size
		update_post_meta( $post_id, 'bingo_card_type', $data['bingo_card_type'] );
		update_post_meta( $post_id, 'bingo_grid_size', $data['bingo_grid_size'] );
		update_post_meta( $post_id, 'lbcg_font_size', $data['lbcg_font_size'] );
		// Title
		if ( ! empty( $data['bingo_card_title'] ) ) {
			$title = trim( wp_strip_all_tags( $data['bingo_card_title'] ) );
			update_post_meta( $post_id, 'bingo_card_title', $title );
		}
		// Set thumbnail
		if ( ! empty( $data['bingo_card_thumbnail'] ) ) {
			if ( ! is_numeric( $data['bingo_card_thumbnail'] ) ) {
				if ( isset( $title ) ) {
					$thumb_name = str_replace( ' ', '-', $title ) . '-' . wp_generate_password( 12, false );
				} else {
					$thumb_name = 'lbcg-thumb-name-' . wp_generate_password( 12, false );
				}
				self::set_as_featured_image( $data['bingo_card_thumbnail'], $post_id, $thumb_name . '.png' );
			}
		}
		// 1-75 special title
		if ( $data['bingo_card_type'] === '1-75' && isset( $data['bingo_card_spec_title'] ) ) {
			if ( is_string( $data['bingo_card_spec_title'] ) && strlen( $data['bingo_card_spec_title'] ) <= 5 ) {
				update_post_meta( $post_id, 'bingo_card_spec_title', $data['bingo_card_spec_title'] );
			}
		}
		// Words/emojis or numbers
		if ( ! in_array( $data['bingo_card_type'], $special_cards ) && ! empty( $data['bingo_card_content'] ) ) {
			$data['bingo_card_content'] = preg_replace( "/(\r?\n){2,}/", "\r\n", $data['bingo_card_content'] );
			update_post_meta( $post_id, 'bingo_card_content', trim( wp_strip_all_tags( $data['bingo_card_content'] ) ) );
		}
		// Square style on click
		if ( ! empty( $data['grid_square'] ) ) {
			update_post_meta( $post_id, 'grid_square', $data['grid_square'] );
		}
		// Header color, image with attributes
		if ( ! empty( $data['bc_header'] ) ) {
			if ( ! empty( $_FILES['bc_header']['size']['image'] ) ) {
				$attach_id                  = self::upload_attachment( $_FILES['bc_header'], $post_id );
				$data['bc_header']['image'] = $attach_id;
			} elseif ( ! empty( $theme_meta_data['bc_header'][0] ) ) {
				$bc_header                  = unserialize( $theme_meta_data['bc_header'][0] );
				$data['bc_header']['image'] = $bc_header['image'];
			} elseif ( empty( $data['bc_header']['image'] ) ) {
				$data['bc_header']['image'] = '0';
			}
			if ( empty( $data['bc_header']['repeat'] ) ) {
				$data['bc_header']['repeat'] = 'no-repeat';
			}
			if ( isset( $data['bc_header']['remove_image'] ) && (int) $data['bc_header']['remove_image'] === 1 ) {
				$data['bc_header']['image'] = '0';
			}
			update_post_meta( $post_id, 'bc_header', $data['bc_header'] );
		}
		// Grid color, image with attributes
		if ( ! empty( $data['bc_grid'] ) ) {
			if ( ! empty( $_FILES['bc_grid']['size']['image'] ) ) {
				$attach_id                = self::upload_attachment( $_FILES['bc_grid'], $post_id );
				$data['bc_grid']['image'] = $attach_id;
			} elseif ( ! empty( $theme_meta_data['bc_grid'][0] ) ) {
				$bc_grid                  = unserialize( $theme_meta_data['bc_grid'][0] );
				$data['bc_grid']['image'] = $bc_grid['image'];
			} elseif ( empty( $data['bc_grid']['image'] ) ) {
				$data['bc_grid']['image'] = '0';
			}
			if ( empty( $data['bc_grid']['repeat'] ) ) {
				$data['bc_grid']['repeat'] = 'no-repeat';
			}
			if ( isset( $data['bc_grid']['remove_image'] ) && (int) $data['bc_grid']['remove_image'] === 1 ) {
				$data['bc_grid']['image'] = '0';
			}
			update_post_meta( $post_id, 'bc_grid', $data['bc_grid'] );
		}
		// Card color, image with attributes
		if ( ! empty( $data['bc_card'] ) ) {
			if ( ! empty( $_FILES['bc_card']['size']['image'] ) ) {
				$attach_id                = self::upload_attachment( $_FILES['bc_card'], $post_id );
				$data['bc_card']['image'] = $attach_id;
			} elseif ( ! empty( $theme_meta_data['bc_card'][0] ) ) {
				$bc_card                  = unserialize( $theme_meta_data['bc_card'][0] );
				$data['bc_card']['image'] = $bc_card['image'];
			} elseif ( empty( $data['bc_card']['image'] ) ) {
				$data['bc_card']['image'] = 0;
			}
			if ( empty( $data['bc_card']['repeat'] ) ) {
				$data['bc_card']['repeat'] = 'no-repeat';
			}
			if ( isset( $data['bc_card']['remove_image'] ) && (int) $data['bc_card']['remove_image'] === 1 ) {
				$data['bc_card']['image'] = '0';
			}
			update_post_meta( $post_id, 'bc_card', $data['bc_card'] );
		}
		// Font
		if ( ! empty( $data['bingo_card_font'] ) ) {
			update_post_meta( $post_id, 'bingo_card_font', $data['bingo_card_font'] );
		}
		// Word wrap
		update_post_meta( $post_id, 'bingo_card_wrap_words', empty( $data['bingo_card_wrap_words'] ) ? 'off' : 'on' );
		// Free square
		update_post_meta( $post_id, 'bingo_card_free_square', ! empty( $data['bingo_card_free_square'] ) && $data['bingo_grid_size'] !== '4x4' && $data['bingo_card_type'] !== '1-90' ? 'on' : 'off' );
		// Custom CSS
		update_post_meta( $post_id, 'bingo_card_custom_css', isset( $data['bingo_card_custom_css'] ) ? trim( wp_strip_all_tags( $data['bingo_card_custom_css'] ) ) : '' );
	}

	/**
	 * Check if correct emails
	 *
	 * @param   string|array  $emails
	 *
	 * @return bool
	 */
	public static function is_valid_emails( $emails ) {
		if ( ! is_array( $emails ) ) {
			$emails = [ $emails ];
		}
		foreach ( $emails as $email ) {
			if ( ! is_email( $email ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Invite emails
	 *
	 * @param   int     $bingo_card_id
	 * @param   string  $author_email
	 * @param   array   $invite_emails
	 *
	 * @return array
	 */
	public static function invite_emails( $bingo_card_id, $author_email, $invite_emails ) {
		$data = get_post_meta( $bingo_card_id );
		// Save card content
		if ( $data['bingo_card_type'][0] === '1-75' ) {
			$content_words = self::get_1_75_bingo_card_numbers( true );
		} elseif ( $data['bingo_card_type'][0] === '1-90' ) {
			$content_words = self::get_1_90_bingo_card_numbers( true );
			foreach ( $content_words as $key => $value ) {
				$content_words[ $key ] = implode( ';', $value );
			}
		} else {
			$content_words = explode( "\r\n", $data['bingo_card_content'][0] );
			shuffle( $content_words );
		}
		update_post_meta( $bingo_card_id, 'bingo_card_own_content', implode( $data['bingo_card_type'][0] === '1-90' ? ':' : "\r\n", $content_words ) );
		update_post_meta( $bingo_card_id, 'author_email', $author_email );
		// Send email
		$subject = "Your Bingo Card";
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		// Get email content
		$email_content = self::get_new_bingo_email_content( $subject, $author_email, $bingo_card_id );
		$sent          = mail( $author_email, $subject, $email_content, $headers );
		if ( ! $sent ) {
			return [
				'success'        => false,
				'errors'         => [ "Invitation fail. Failed to email your card." ],
				'failed_invites' => []
			];
		}
		// Invite
		$failed_to_invite = [];
		$invite_subject   = "Get Your New Bingo Card";
		foreach ( $invite_emails as $user_email ) {
			// Create new child bingo card
			$new_bc_id = self::create_child_bingo_card( $bingo_card_id, $data, $user_email );
			if ( $new_bc_id === false ) {
				$failed_to_invite[] = $user_email;
				continue;
			}
			// Get email content
			$email_content = self::get_new_bingo_email_content( $invite_subject, $user_email, $new_bc_id );
			$sent          = mail( $user_email, $invite_subject, $email_content, $headers );
			if ( ! $sent ) {
				// Delete not used bingo card
				wp_delete_post( $new_bc_id );
				$failed_to_invite[] = $user_email;
			}
		}

		return [
			'success'        => true,
			'errors'         => [],
			'failed_invites' => $failed_to_invite
		];
	}

	/**
	 * Get new bingo card email content
	 *
	 * @param   string  $title
	 * @param   string  $user_email
	 * @param   int     $bc_id
	 *
	 * @return false|string
	 */
	public static function get_new_bingo_email_content( $title, $user_email, $bc_id ) {
		// Get bingo card link
		$bc_link = get_permalink( $bc_id );
		/**
		 * Get email content
		 * Necessary variables for email template
		 *
		 * @var string $bc_link The Bingo Card permalink
		 */
		ob_start();
		include 'partials/lbcg-includes-display-email.php';
		$content = ob_get_clean();

		return $content;
	}

	/**
	 * Get user password reset link
	 *
	 * @param   int  $user_id
	 *
	 * @return string
	 */
	public static function get_password_reset_link( $user_id ) {
		try {
			$user = get_user_by( 'id', $user_id );
			if ( $user instanceof WP_User ) {
				$rp_key = get_password_reset_key( $user );
				if ( ! is_wp_error( $rp_key ) ) {
					return network_site_url( "wp-login.php?action=rp&key=$rp_key&login=" . rawurlencode( $user->user_login ), 'login' );
				}
			}

			return '';
		}
		catch ( \Exception $e ) {
			return '';
		}
	}

	/**
	 * Create child bingo card
	 *
	 * @param   int     $parent_bc_id
	 * @param   array   $parent_bc_meta_data
	 * @param   string  $user_email
	 *
	 * @return false|int
	 */
	public static function create_child_bingo_card( $parent_bc_id, $parent_bc_meta_data, $user_email ) {
		// Collect data
		$result = self::collect_card_data_from( $parent_bc_meta_data, true );
		if ( $result['success'] === false ) {
			return false;
		}
		// Create bingo card post
		$bc_result = self::insert_bingo_card( $result['data'], 'publish' );
		if ( $bc_result === false ) {
			return false;
		}
		// Save card data
		self::save_bingo_meta_fields( $bc_result['id'], $result['data'] );
		// Save card content
		if ( $result['data']['bingo_card_type'] === '1-75' ) {
			$content_words = self::get_1_75_bingo_card_numbers( true );
		} elseif ( $result['data']['bingo_card_type'] === '1-90' ) {
			$content_words = self::get_1_90_bingo_card_numbers( true );
			foreach ( $content_words as $key => $value ) {
				$content_words[ $key ] = implode( ';', $value );
			}
		} else {
			$content_words = explode( "\r\n", $result['data']['bingo_card_content'] );
			shuffle( $content_words );
		}
		update_post_meta( $bc_result['id'], 'bingo_card_own_content', implode( $result['data']['bingo_card_type'] === '1-90' ? ':' : "\r\n", $content_words ) );
		update_post_meta( $bc_result['id'], 'parent_bingo_card_id', $parent_bc_id );
		update_post_meta( $bc_result['id'], 'author_email', $user_email );

		return $bc_result['id'];
	}

	/**
	 * Insert bingo card
	 *
	 * @param   array   $data
	 * @param   string  $status
	 *
	 * @return array|false
	 */
	public static function insert_bingo_card( $data, $status = 'draft' ) {
		if ( ! empty( $_POST['bc'] ) ) {
			$bc_posts = get_posts( [
				'name'           => $_POST['bc'],
				'post_type'      => 'bingo_card',
				'posts_per_page' => 1,
				'post_status'    => 'publish'
			] );
			if ( isset( $bc_posts[0] ) && ! empty( $bc_posts[0]->ID ) ) {
				return [
					'id'      => $bc_posts[0]->ID,
					'uniq_id' => $_POST['bc']
				];
			}
		}
		$title       = "Bingo Card: " . trim( $data['bingo_card_title'] );
		$uniq_string = wp_generate_password( 16, false );
//        $uniq_string = wp_generate_uuid4();
//        $uniq_string = str_replace('-', '', $uniq_string);
		// Create new card
		$args = [
			'post_author' => 0,
			'post_title'  => $title,
			'post_type'   => 'bingo_card',
			'post_name'   => $uniq_string,
			'post_status' => $status
		];
		$id   = wp_insert_post( $args );
		if ( is_wp_error( $id ) || $id === 0 ) {
			return false;
		}

		return [
			'id'      => $id,
			'uniq_id' => $uniq_string
		];
	}

	/**
	 * Generate all contents
	 *
	 * @param   int    $post_id
	 * @param   int    $count
	 * @param   int    $wanted_count
	 * @param   array  $data
	 *
	 * @return array
	 */
	public static function generate_all_content_info( $post_id, $count, $wanted_count, $data ) {
		if ( ! empty( $data['all_content'][0] ) ) {
			return explode( '|', $data['all_content'][0] );
		}
		$all = [];
		if ( $data['bingo_card_type'][0] === '1-75' ) {
			for ( $i = 0; $i < $count; $i ++ ) {
				$content_items = self::get_1_75_bingo_card_numbers( true );
				$all[]         = implode( ';', $content_items );
			}
		} elseif ( $data['bingo_card_type'][0] === '1-90' ) {
			for ( $i = 0; $i < $count; $i ++ ) {
				$content_items = self::get_1_90_bingo_card_numbers( true );
				foreach ( $content_items as $key => $value ) {
					$content_items[ $key ] = implode( ';', $value );
				}
				$all[] = implode( ':', $content_items );
			}
		} else {
			$items_count   = $data['bingo_grid_size'][0][0] ** 2;
			$content_items = explode( "\r\n", $data['bingo_card_content'][0] );
			$indexes       = array_keys( $content_items );
			for ( $i = 0; $i < $count; $i ++ ) {
				shuffle( $indexes );
				$all[] = implode( ';', array_slice( $indexes, 0, $items_count ) );
			}
		}
		update_post_meta( $post_id, 'all_content', implode( '|', $all ) );

		return array_slice( $all, 0, $wanted_count );
	}

	/**
	 * Show bingo theme breadcrumb
	 *
	 * @param   string       $wp_theme_name
	 * @param   int|WP_Term  $data
	 * @param   string       $type
	 *
	 * @return void
	 */
	public static function show_breadcrumb( $wp_theme_name, $data, $type ) {
		if ( ! function_exists( 'link_trk' ) || $wp_theme_name !== 'BNBS' ) {
			return;
		}
		if ( $type === 'bingo_theme' ) {
			$current_bt_category = get_the_terms( $data, 'ubud-category' );
			$bt_post_title       = get_the_title( $data );
			$links               = [
				'Home'                        => SITEURL,
				'Bingo Card Generators'       => SITEURL . '/bingo-cards/'
			];
            if ( isset( $current_bt_category[0] ) ) {
	            $links[$current_bt_category[0]->name] = SITEURL . '/bingo-cards/category/' . $current_bt_category[0]->slug . '/';
            }
			$links[$bt_post_title] = '';
		} elseif ( $type === 'ubud_category' ) {
			$links = [
				'Home'                  => SITEURL,
				'Bingo Card Generators' => SITEURL . '/bingo-cards/',
				$data->name             => ''
			];
		} else {
			$links = [
				'Home'                  => SITEURL,
				'Bingo Card Generators' => SITEURL . '/bingo-cards/'
			];
		}
		?>
        <nav aria-label="lbcg-breadcrumb">
            <ol class="lbcg-breadcrumb">
				<?php
				$i   = 1;
				$cnt = count( $links );
				foreach ( $links as $name => $link ) {
					if ( $i === $cnt ) {
						echo '<li class="breadcrumb-item active" aria-current="page">' . $name . '</li>';
					} else {
						$attributes = array(
							"echo_title" => $name,
							"class"      => '',
							"link"       => $link,
							"display"    => '',
							"place"      => 'breadcrumbs',
							"element"    => 'text',
							"idx"        => - 1
						);
						$link_track = link_trk( $attributes );
						echo '<li class="breadcrumb-item">' . $link_track . '</li>';
					}
					$i ++;
				}
				?>
            </ol>
        </nav>
		<?php
	}

	/**
	 * Upload file
	 *
	 * @param   array  $file
	 * @param   int    $post_id
	 *
	 * @return int
	 */
	public static function upload_attachment( $file, $post_id ) {
		$upload_id     = 0;
		$wp_upload_dir = wp_upload_dir();
		$new_file_path = $wp_upload_dir['path'] . '/' . $file['name']['image'];
		$new_file_mime = mime_content_type( $file['tmp_name']['image'] );
		$i             = 1;
		while ( file_exists( $new_file_path ) ) {
			$i ++;
			$new_file_path = $wp_upload_dir['path'] . '/' . $i . '_' . $file['name']['image'];
		}
		if ( move_uploaded_file( $file['tmp_name']['image'], $new_file_path ) ) {
			$upload_id = wp_insert_attachment( array(
				'guid'           => $new_file_path,
				'post_mime_type' => $new_file_mime,
				'post_title'     => preg_replace( '/\.[^.]+$/', '', $file['name']['image'] ),
				'post_content'   => '',
				'post_status'    => 'inherit'
			), $new_file_path );
			wp_update_attachment_metadata( $upload_id, wp_generate_attachment_metadata( $upload_id, $new_file_path ) );
		}
		if ( is_wp_error( $upload_id ) ) {
			$upload_id = 0;
		}

		return $upload_id;
	}

	/**
	 * Save base64 image as post thumbnail
	 *
	 * @param $base64
	 * @param $post_id
	 * @param $filename
	 *
	 * @return bool
	 */
	public static function set_as_featured_image( $base64, $post_id, $filename ) {
		if ( empty( $base64 ) ) {
			return false;
		}
		$upload_dir   = wp_upload_dir();
		$upload_path  = str_replace( '/', DIRECTORY_SEPARATOR, $upload_dir['path'] ) . DIRECTORY_SEPARATOR;
		$img          = str_replace( 'data:image/png;base64,', '', $base64 );
		$img          = str_replace( ' ', '+', $img );
		$decoded      = base64_decode( $img );
		$image_upload = file_put_contents( $upload_path . $filename, $decoded );
		if ( ! function_exists( 'wp_handle_sideload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}
		$wp_filetype = wp_check_filetype( basename( $filename ), null );
		$attachment  = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
			'post_content'   => '',
			'post_status'    => 'inherit'
		);
		$attach_id   = wp_insert_attachment( $attachment, wp_upload_dir()['path'] . '/' . $filename, $post_id );
		$attach_data = wp_generate_attachment_metadata( $attach_id, wp_upload_dir()['path'] . '/' . $filename );
		wp_update_attachment_metadata( $attach_id, $attach_data );
		set_post_thumbnail( $post_id, $attach_id );

		return true;
	}
}