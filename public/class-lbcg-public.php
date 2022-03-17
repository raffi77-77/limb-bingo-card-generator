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
		add_shortcode( 'lbcg-ubud-categories', array( $this, 'show_ubud_categeories_template' ) );
		add_filter( 'single_template', array( $this, 'get_custom_post_type_template' ) );
		add_filter( 'template_include', array( $this, 'get_custom_template' ) );
		add_filter( 'taxonomy_template', array( $this, 'get_custom_taxonomy_template' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_and_styles' ) );
		add_action( 'wp_head', array( $this, 'add_custom_css' ) );
		add_action( 'terms_clauses', array( $this, 'custom_terms_clauses' ), 15, 3 );
		add_filter( "rank_math/opengraph/facebook/image", array( $this, 'custom_opengraph_image' ) );
		add_filter( "rank_math/opengraph/twitter/image", array( $this, 'custom_opengraph_image' ) );
	}

	/**
	 * Show UBUD Categories
	 *
	 * @param   array   $atts
	 * @param   string  $content
	 * @param   string  $tag
	 *
	 * @return string
	 */
	public function show_ubud_categeories_template( $atts = array(), $content = '', $tag = '' ) {
		$attributes = shortcode_atts( array(
			'slugs' => 'all'
		), $atts );
		ob_start();
		require( $this->attributes['public_templates_path'] . '/lbcg-public-display-generators.php' );

		return ob_get_clean();
	}

	/**
	 * Enqueue public scripts and styles
	 */
	public function enqueue_scripts_and_styles() {
		if ( is_singular( [ 'bingo_theme', 'bingo_card' ] ) || is_tax( 'ubud-category' ) ) {
			wp_enqueue_script( 'lbcg-vanilla-js', $this->attributes['includes_url'] . 'js/vanilla.js' );
            if ( is_singular( 'bingo_theme' ) ) {
	            wp_enqueue_script( 'html2canvas-js', $this->attributes['includes_url'] . 'js/html2canvas.min.js', [], $this->attributes['plugin_version'] );
            }
			wp_enqueue_script( 'lbcg-public-js', $this->attributes['public_url'] . 'js/lbcg-public.min.js', [], $this->attributes['plugin_version'] );
			wp_localize_script( 'lbcg-public-js', 'LBCG', [
				'fonts'          => LBCG_Helper::$fonts,
				'freeSquareWord' => LBCG_Helper::$free_space_word,
				'ajaxUrl'        => admin_url( 'admin-ajax.php' )
			] );
			wp_enqueue_style( 'lbcg-public-css', $this->attributes['public_url'] . 'css/lbcg-public.min.css?', [], $this->attributes['plugin_version'] );
		}
		if ( is_page( 'bingo-cards' ) || is_post_type_archive( 'bingo_theme' ) ) {
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
			if ( preg_match( '/bingo-cards\/([^\/]+)\/invitation\/\?bc=([a-zA-z0-9]+)$/', $_SERVER['REQUEST_URI'] ) ) {
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
	 * Get custom template
	 *
	 * @param   string  $template
	 *
	 * @return string
	 */
	public function get_custom_template( $template ) {
		if ( is_post_type_archive( 'bingo_theme' ) ) {
			$template = $this->attributes['public_templates_path'] . '/lbcg-public-display-archive-bingo_theme.php';
		}

		return $template;
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
			if ( ! empty( $_GET['bc'] ) && empty( $this->dev_mode_card_id ) ) {
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
			echo trim( strip_tags( $this->data['bingo_card_custom_css'][0], '<style><link>' ) );
		}
	}

	/**
     * Set terms custom clauses
     *
	 * @param string[] $clauses
	 * @param string[] $taxonomies
	 * @param array $args
	 *
	 * @return string[]
	 */
    public function custom_terms_clauses( $clauses, $taxonomies, $args ) {
        // If is the custom taxonomy
        if ( $taxonomies === [ 'ubud-category' ] ) {
            // If set custom order by field
	        if ( isset( $args['orderby'] ) && $args['orderby'] === '_lc_meta_value' ) {
                // Set own order
		        $clauses['orderby'] = ' ORDER BY wp_termmeta.meta_value ';
	        }
        }
        return $clauses;
    }

	/**
     * Change the OpenGraph image.
	 *
	 * @param string $attachment_url The image we are about to add.
	 *
	 * @return string
	 */
	public function custom_opengraph_image( $attachment_url ) {
		if ( is_singular( 'bingo_theme' ) ) {
			if ( ! empty( $this->dev_mode_card_id ) ) {
				$new_url = get_the_post_thumbnail_url( $this->dev_mode_card_id );
			} else if ( ! empty( $_GET['bc'] ) ) {
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

				$new_url = get_the_post_thumbnail_url( $this->dev_mode_card_id );
			}
		}

		return ! empty( $new_url ) ? $new_url : $attachment_url;
	}

	/**
	 * Show social share buttons
     * Facebook, Twitter, Pinterest, Reddit, WhatsApp and Email
	 *
	 * @param string $url
	 * @param string|null $media_url
	 * @param string|null $text
	 */
	public function show_social_container( $url, $media_url = null, $text = null ) {
		$url = urlencode( $url );
		if ( ! empty( $media_url ) ) {
			$media_url = urlencode( $media_url );
		}
		if ( ! empty( $text ) ) {
			$text = rawurlencode( wp_strip_all_tags( $text ) );
		}
		// Facebook
		$args = [
			'u' => $url,
		];
		if ( ! empty( $text ) ) {
			$args['quote'] = $text;
		}
		$facebook_url = add_query_arg( $args, 'https://www.facebook.com/sharer/sharer.php' );
		// Twitter
		$args = [
			'url' => $url,
		];
		if ( ! empty( $text ) ) {
			$args['text'] = $text;
		}
		$twitter_url = add_query_arg( $args, 'https://twitter.com/intent/tweet' );
		// Pinterest
		$args = [
			'url' => $url,
		];
		if ( ! empty( $media_url ) ) {
			$args['media'] = $media_url;
		}
		if ( ! empty( $text ) ) {
			$args['description'] = $text;
		}
		$pinterest_url = add_query_arg( $args, 'https://pinterest.com/pin/create/link' );
		// Reddit
		$args = [
			'url' => $url,
		];
		if ( ! empty( $text ) ) {
			$args['title'] = $text;
		}
		$reddit_url = add_query_arg( $args, 'https://www.reddit.com/submit' );
		// WhatsApp
		$args = [
			'text' => $url,
		];
		if ( ! empty( $text ) ) {
			$args['text'] = $text . ' ' . $args['text'];
		}
		$whatsapp_url = add_query_arg( $args, 'https://wa.me' );
		// Email
		$args      = [
			'subject' => ! empty( $text ) ? $text : '',
			'body'    => $url
		];
		$email_url = add_query_arg( $args, 'mailto:' )
		?>
        <a href="<?php echo $facebook_url; ?>" target="_blank">
            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M28.5255 15.338H23.1316V11.8004C23.1316 10.4718 24.0121 10.1621 24.6323 10.1621C25.2511 10.1621 28.4388 10.1621 28.4388 10.1621V4.32149L23.1965 4.30103C17.3771 4.30103 16.0528 8.65712 16.0528 11.4448V15.338H12.6873V21.3564H16.0528C16.0528 29.0801 16.0528 38.3864 16.0528 38.3864H23.1316C23.1316 38.3864 23.1316 28.9884 23.1316 21.3564H27.9082L28.5255 15.338Z"
                      fill="#1D3C78"/>
            </svg>
        </a>
        <a href="<?php echo $twitter_url; ?>" target="_blank">
            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M37.2793 10.6293C36.0016 11.1972 34.6264 11.5804 33.1836 11.7518C34.6568 10.8699 35.7864 9.47286 36.3191 7.80634C34.9411 8.62408 33.4157 9.21745 31.7908 9.53777C30.4905 8.15206 28.637 7.28564 26.586 7.28564C22.6483 7.28564 19.4556 10.4783 19.4556 14.4167C19.4556 14.9748 19.5184 15.5188 19.6405 16.0416C13.7138 15.7438 8.45885 12.9054 4.94166 8.59092C4.32783 9.64361 3.97646 10.8684 3.97646 12.1765C3.97646 14.6502 5.23517 16.8332 7.14863 18.1117C5.98023 18.0743 4.88027 17.7533 3.9186 17.2191C3.9179 17.2488 3.9179 17.2791 3.9179 17.3095C3.9179 20.7639 6.37605 23.6453 9.63853 24.3015C9.04022 24.4638 8.41016 24.5513 7.75964 24.5513C7.29962 24.5513 6.853 24.5068 6.41768 24.4229C7.32573 27.2557 9.95886 29.318 13.0788 29.3751C10.6383 31.2879 7.5642 32.4281 4.2227 32.4281C3.64767 32.4281 3.0797 32.3942 2.52161 32.3279C5.67826 34.3521 9.42616 35.5318 13.4528 35.5318C26.5697 35.5318 33.7417 24.6663 33.7417 15.2429C33.7417 14.9339 33.7353 14.6255 33.7219 14.3193C35.1147 13.316 36.324 12.0594 37.2793 10.6293Z"
                      fill="#1d9bf0"/>
            </svg>
        </a>
        <a href="<?php echo $pinterest_url; ?>" target="_blank">
            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M21.4304 3.26099C11.8447 3.26099 7.01099 10.1331 7.01099 15.865C7.01099 19.3349 8.32473 22.4217 11.142 23.5711C11.6041 23.7616 12.0183 23.5781 12.1524 23.0666C12.2455 22.7138 12.4663 21.8206 12.5644 21.4474C12.6992 20.9415 12.647 20.7651 12.2737 20.3227C11.4616 19.3653 10.9416 18.1249 10.9416 16.3667C10.9416 11.2683 14.7566 6.70409 20.8751 6.70409C26.2931 6.70409 29.2698 10.0146 29.2698 14.4349C29.2698 20.2529 26.6952 25.1628 22.874 25.1628C20.7629 25.1628 19.1839 23.418 19.6891 21.2766C20.2952 18.7204 21.4699 15.9631 21.4699 14.1166C21.4699 12.4656 20.583 11.0884 18.7493 11.0884C16.5917 11.0884 14.8582 13.3208 14.8582 16.3102C14.8582 18.2145 15.5016 19.5028 15.5016 19.5028C15.5016 19.5028 13.2939 28.8585 12.9066 30.4968C12.1361 33.76 12.7909 37.7591 12.8466 38.1626C12.8791 38.4025 13.1867 38.4604 13.3264 38.2798C13.5247 38.0194 16.0957 34.8472 16.9685 31.6765C17.2161 30.7797 18.3873 26.1315 18.3873 26.1315C19.0887 27.4693 21.1376 28.6447 23.3164 28.6447C29.8011 28.6447 34.2016 22.7329 34.2016 14.8194C34.2023 8.83416 29.1336 3.26099 21.4304 3.26099Z"
                      fill="#e60023"/>
            </svg>
        </a>
        <a href="<?php echo $reddit_url; ?>" target="_blank">
            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M37.4237 20.0676C37.4237 17.8543 35.623 16.0535 33.4096 16.0535C32.4251 16.0535 31.4967 16.4097 30.7637 17.0546C28.1093 15.3559 24.5673 14.2856 20.6604 14.1704L23.0509 5.99379L28.9205 7.08431C28.9202 7.13199 28.9109 7.1775 28.9134 7.22568C28.9847 8.62258 30.1789 9.70088 31.5759 9.62963C32.9727 9.55838 34.0511 8.36409 33.9798 6.96727C33.9086 5.57037 32.7143 4.49207 31.3174 4.56332C30.4155 4.60931 29.6522 5.12663 29.2395 5.86124C29.2388 5.86103 29.2381 5.86053 29.2374 5.86039L22.1644 4.54639L19.3471 14.1681C15.4307 14.2725 11.8776 15.3366 9.21169 17.0322C8.48259 16.4012 7.56386 16.0535 6.59045 16.0535C4.37726 16.0535 2.57642 17.8543 2.57642 20.0676C2.57642 21.4365 3.28118 22.6989 4.42241 23.4354C4.35307 23.836 4.31349 24.2427 4.31349 24.6556C4.31349 30.4494 11.3367 35.1634 19.9702 35.1634C28.6037 35.1634 35.6269 30.4493 35.6269 24.6556C35.6269 24.2546 35.59 23.8596 35.5245 23.4702C36.6972 22.7393 37.4237 21.4581 37.4237 20.0676ZM12.5293 22.7996C12.5293 21.4382 13.6333 20.3335 14.9967 20.3335C16.3587 20.3335 17.4628 21.4382 17.4628 22.7996C17.4628 24.1623 16.3587 25.2663 14.9967 25.2663C13.6333 25.2663 12.5293 24.1623 12.5293 22.7996ZM25.785 29.4713C25.7114 29.5482 23.9414 31.3489 19.9414 31.3489C15.9208 31.3489 14.3124 29.5232 14.2461 29.4458C14.0194 29.1811 14.0503 28.7824 14.315 28.5565C14.5784 28.3319 14.9731 28.3608 15.2004 28.6215C15.2365 28.661 16.5789 30.088 19.9414 30.088C23.3624 30.088 24.8623 28.6104 24.8774 28.596C25.1198 28.3491 25.5184 28.3431 25.7673 28.5855C26.0148 28.8271 26.024 29.2212 25.785 29.4713ZM25.2944 25.2663C23.931 25.2663 22.827 24.1623 22.827 22.7996C22.827 21.4382 23.9311 20.3335 25.2944 20.3335C26.6565 20.3335 27.7605 21.4382 27.7605 22.7996C27.7605 24.1623 26.6565 25.2663 25.2944 25.2663Z"
                      fill="#ff4500"/>
            </svg>
        </a>
        <a href="<?php echo $whatsapp_url; ?>" target="_blank" data-action="share/whatsapp/share">
            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3.33994 36.6666L5.59327 28.3866C4.1085 25.8413 3.32843 22.9466 3.33327 19.9999C3.33327 10.7949 10.7949 3.33325 19.9999 3.33325C29.2049 3.33325 36.6666 10.7949 36.6666 19.9999C36.6666 29.2049 29.2049 36.6666 19.9999 36.6666C17.0546 36.6713 14.161 35.8919 11.6166 34.4083L3.33994 36.6666ZM13.9849 12.1799C13.7697 12.1933 13.5594 12.25 13.3666 12.3466C13.1858 12.449 13.0208 12.577 12.8766 12.7266C12.6766 12.9149 12.5633 13.0783 12.4416 13.2366C11.8256 14.0382 11.4943 15.0223 11.4999 16.0333C11.5033 16.8499 11.7166 17.6449 12.0499 18.3883C12.7316 19.8916 13.8533 21.4833 15.3349 22.9583C15.6916 23.3133 16.0399 23.6699 16.4149 24.0016C18.2539 25.6207 20.4454 26.7883 22.8149 27.4116L23.7633 27.5566C24.0716 27.5733 24.3799 27.5499 24.6899 27.5349C25.1753 27.5099 25.6493 27.3784 26.0783 27.1499C26.3549 27.0033 26.4849 26.9299 26.7166 26.7833C26.7166 26.7833 26.7883 26.7366 26.9249 26.6333C27.1499 26.4666 27.2883 26.3483 27.4749 26.1533C27.6133 26.0099 27.7333 25.8416 27.8249 25.6499C27.9549 25.3783 28.0849 24.8599 28.1383 24.4283C28.1783 24.0983 28.1666 23.9183 28.1616 23.8066C28.1549 23.6283 28.0066 23.4433 27.8449 23.3649L26.8749 22.9299C26.8749 22.9299 25.4249 22.2983 24.5399 21.8949C24.4466 21.8542 24.3466 21.8311 24.2449 21.8266C24.1309 21.8149 24.0157 21.8277 23.907 21.8642C23.7984 21.9007 23.6988 21.9601 23.6149 22.0383V22.0349C23.6066 22.0349 23.4949 22.1299 22.2899 23.5899C22.2208 23.6829 22.1255 23.7531 22.0163 23.7917C21.907 23.8303 21.7888 23.8355 21.6766 23.8066C21.568 23.7775 21.4616 23.7408 21.3583 23.6966C21.1516 23.6099 21.0799 23.5766 20.9383 23.5149L20.9299 23.5116C19.9765 23.0953 19.0936 22.533 18.3133 21.8449C18.1033 21.6616 17.9083 21.4616 17.7083 21.2683C17.0526 20.6403 16.4811 19.9299 16.0083 19.1549L15.9099 18.9966C15.8393 18.8902 15.7822 18.7754 15.7399 18.6549C15.6766 18.4099 15.8416 18.2133 15.8416 18.2133C15.8416 18.2133 16.2466 17.7699 16.4349 17.5299C16.5918 17.3305 16.7381 17.123 16.8733 16.9083C17.0699 16.5916 17.1316 16.2666 17.0283 16.0149C16.5616 14.8749 16.0783 13.7399 15.5816 12.6133C15.4833 12.3899 15.1916 12.2299 14.9266 12.1983C14.8366 12.1883 14.7466 12.1783 14.6566 12.1716C14.4328 12.1605 14.2085 12.1627 13.9849 12.1783V12.1799Z"
                      fill="#4ecb5c"/>
            </svg>
        </a>
        <a href="<?php echo $email_url; ?>" target="_blank">
            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M33.3333 5H6.66667C3 5 0 8 0 11.6667V28.3333C0 32 3 35 6.66667 35H33.3333C37 35 40 32 40 28.3333V11.6667C40 8 37 5 33.3333 5ZM36 14.6667L22.8333 23.5C22 24 21 24.3333 20 24.3333C19 24.3333 18 24 17.1667 23.5L4 14.6667C3.33333 14.1667 3.16667 13.1667 3.66667 12.3333C4.16667 11.6667 5.16667 11.5 6 12L19.1667 20.8333C19.6667 21.1667 20.5 21.1667 21 20.8333L34.1667 12C35 11.5 36 11.6667 36.5 12.5C36.8333 13.1667 36.6667 14.1667 36 14.6667Z"
                      fill="#382063"/>
            </svg>
        </a>
		<?php
	}
}