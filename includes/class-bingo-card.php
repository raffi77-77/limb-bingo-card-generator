<?php

class BingoCard
{
    const VERSION = '1.0.0';
    private static $instance = null;
    public static $plugin_path = null;
    public static $plugin_url = null;
    public static $logs_path = null;
    public static $admin_path = null;
    public static $admin_templates_path = null;
    public static $ajax_path = null;
    public static $includes_path = null;
    public static $templates_path = null;

    /**
     * Get instance
     *
     * @return BingoCard
     */
    public static function get_instance($plugin_dir, $plugin_url)
    {
        if (null == self::$instance) {
            // Create new instance
            self::$instance = new BingoCard($plugin_dir, $plugin_url);
        }
        return self::$instance;
    }

    /**
     * Construct
     */
    private function __construct($plugin_dir, $plugin_url)
    {
        // Set plugin path and url params
        self::$plugin_path = $plugin_dir;
        self::$plugin_url = $plugin_url;
        self::$logs_path = self::$plugin_path . 'logs';
        self::$admin_path = self::$plugin_path . 'admin';
        self::$admin_templates_path = self::$plugin_path . 'admin/templates';
        self::$ajax_path = self::$plugin_path . 'ajax';
        self::$includes_path = self::$plugin_path . 'includes';
        self::$templates_path = self::$plugin_path . 'public/templates';

        $this->load_dependencies();
        $this->init();
    }

    private function load_dependencies()
    {
        require_once self::$includes_path . '/class-bingo-card-helper.php';
        require_once self::$admin_path . '/class-bingo-card-admin.php';
        require_once self::$ajax_path . '/class-bingo-card-ajax.php';
    }

    private function init()
    {
        $this->register_custom_post_types();

        if (BingoCardHelper::is_request('admin')) {
            add_action('add_meta_boxes', array($this, 'add_custom_meta_boxes'));
            add_action('save_post', array($this, 'save_custom_fields'));
            add_action('admin_print_scripts-post-new.php', array($this, 'enqueue_admin_script'), 11);
            add_action('admin_print_scripts-post.php', array($this, 'enqueue_admin_script'), 11);
        } elseif (BingoCardHelper::is_request('ajax')) {
//            $ajax_obj = new BingoCardAjax();
//            add_action('wp_ajax_nopriv_{$action}', array($ajax_obj, '{action}'));
//            add_action('wp_ajax_{$action}', array($ajax_obj, '{action}'));
        } elseif (BingoCardHelper::is_request('public')) {
            add_filter('single_template', array($this, 'get_custom_post_type_template'));
        }
    }

    /**
     * Register custom post types for
     * Bingo Card and Bingo Theme
     */
    private function register_custom_post_types()
    {
        $this->register_bingo_theme_post_type();
        $this->register_bingo_card_post_type();
    }

    /**
     * Add meta boxes
     */
    public function add_custom_meta_boxes()
    {
        add_meta_box(
            'bingo-theme-custom-fields',
            'Custom Fields',
            array($this, 'get_bingo_theme_custom_fields_template'),
            'bingo_theme'
        );
    }

    /**
     * Register custom post type for Bingo Card
     */
    private function register_bingo_card_post_type()
    {
        // Custom post type settings
        $labels = array(
            'name' => __('Bingo cards', 'textdomain'),
            'singular_name' => __('Bingo card', 'textdomain'),
            'menu_name' => __('Bingo cards', 'textdomain'),
            'name_admin_bar' => __('Bingo card', 'textdomain'),
            'add_new' => __('Add new', 'textdomain'),
            'add_new_item' => __('Add new card', 'textdomain'),
            'new_item' => __('New card', 'textdomain'),
            'edit_item' => __('Edit card', 'textdomain'),
            'view_item' => __('View card', 'textdomain'),
            'all_items' => __('All cards', 'textdomain'),
            'search_items' => __('Search Bingo cards', 'textdomain'),
            'not_found' => __('No cards found.', 'textdomain')
        );
        $supports = array('title',
            'editor',
            'author',
//            'excerpt',
//            'custom-fields',
//            'comments',
//            'revisions',
//            'post-formats'
        );
        // Register bingo_card custom post type
        $result = register_post_type('bingo_card',
            array(
                'labels' => $labels,
                'description' => 'Bingo card ...',
                'public' => true,
                'publicly_queryable' => true,
                'query_var' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'capability_type' => 'post',
                'has_archive' => true,
                'hierarchical' => false,
                'rewrite' => array('slug' => 'bingo-card'),
                'supports' => $supports,
                'taxonomies' => array('category', 'post_tag'),
            )
        );
        if ($result instanceof WP_Error) {
            // Log
            file_put_contents(self::$logs_path . '/error.log', '[' . date('Y-m-d H:i:s') . ']' . PHP_EOL . $result->get_error_message(), FILE_APPEND);
        }
    }

    /**
     * Register custom post types for Bingo Theme
     */
    private function register_bingo_theme_post_type()
    {
        // Custom post type settings
        $labels = array(
            'name' => __('Bingo themes', 'textdomain'),
            'singular_name' => __('Bingo theme', 'textdomain'),
            'menu_name' => __('Bingo themes', 'textdomain'),
            'name_admin_bar' => __('Bingo theme', 'textdomain'),
            'add_new' => __('Add new', 'textdomain'),
            'add_new_item' => __('Add new theme', 'textdomain'),
            'new_item' => __('New theme', 'textdomain'),
            'edit_item' => __('Edit theme', 'textdomain'),
            'view_item' => __('View theme', 'textdomain'),
            'all_items' => __('All themes', 'textdomain'),
            'search_items' => __('Search Bingo themes', 'textdomain'),
            'not_found' => __('No themes found.', 'textdomain')
        );
        $supports = array('title',
            'editor',
            'author',
//            'excerpt',
//            'custom-fields',
//            'comments',
//            'revisions',
//            'post-formats'
        );
        // Register bingo_card custom post type
        $result = register_post_type('bingo_theme',
            array(
                'labels' => $labels,
                'description' => 'Bingo theme ...',
                'public' => true,
                'publicly_queryable' => true,
                'query_var' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'capability_type' => 'post',
                'has_archive' => true,
                'hierarchical' => false,
                'rewrite' => array('slug' => 'bingo-theme'),
                'supports' => $supports,
                'taxonomies' => array('category', 'post_tag'),
            )
        );
        if ($result instanceof WP_Error) {
            // Log
            file_put_contents(self::$logs_path . '/error.log', '[' . date('Y-m-d H:i:s') . ']' . PHP_EOL . $result->get_error_message(), FILE_APPEND);
        }
    }

    public function enqueue_admin_script()
    {
        global $post_type;
        if ('bingo_theme' === $post_type) {
            wp_enqueue_script('jquery-3.6.0', self::$plugin_url . '/admin/js/jquery-3.6.0.min.js');
            wp_enqueue_script('bingo-theme-admin-script', self::$plugin_url . '/admin/js/bingo-theme.js?ver=' . self::VERSION);
            wp_enqueue_style('bingo-theme-admin-style', self::$plugin_url . '/admin/css/bingo-theme.css?ver=' . self::VERSION);
        }
    }

    /**
     * Get custom post type template
     *
     * @param $single_template
     * @return mixed|string
     */
    public function get_custom_post_type_template($single_template)
    {
        global $post;
        if ('bingo_theme' === $post->post_type) {
            $single_template = self::$templates_path . '/bingo-theme-template.php';
        }
        return $single_template;
    }

    /**
     * Get bingo theme custom fields template
     */
    public function get_bingo_theme_custom_fields_template()
    {
        include(self::$admin_templates_path . '/bingo-theme-custom-fields-template.php');
    }

    /**
     * Save custom fields
     *
     * @param $post_id
     */
    public function save_custom_fields($post_id)
    {
        global $post_type;
        if ($post_type === 'bingo_theme') {
            if (array_key_exists('bingo_card_type', $_POST)) {
                update_post_meta($post_id, 'bingo_card_type', $_POST['bingo_card_type']);
            }
            if (array_key_exists('bingo_grid_size', $_POST)) {
                update_post_meta($post_id, 'bingo_grid_size', $_POST['bingo_grid_size']);
            }
        }
    }
}
