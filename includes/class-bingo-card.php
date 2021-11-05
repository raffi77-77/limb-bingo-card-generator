<?php

/**
 * The core plugin class
 */
class BingoCard
{
    const VERSION = '1.0.0';
    private static $instance = null;
    protected $attributes = [];

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
     * Construct Bingo Card object
     */
    private function __construct($plugin_dir, $plugin_url)
    {
        // Set plugin path and url params
        $this->attributes = [
            'plugin_path' => $plugin_dir,
            'plugin_url' => $plugin_url,
            'logs_path' => $plugin_dir . 'logs',
            'admin_path' => $plugin_dir . 'admin',
            'admin_templates_path' => $plugin_dir . 'admin/templates',
            'ajax_path' => $plugin_dir . 'ajax',
            'includes_path' => $plugin_dir . 'includes',
            'public_path' => $plugin_dir . 'public',
            'templates_path' => $plugin_dir . 'public/templates'
        ];

        // Start
        $this->load_dependencies();
        $this->init();
    }

    /**
     * Load classes
     */
    private function load_dependencies()
    {
        require_once $this->attributes['admin_path'] . '/class-bingo-card-admin.php';
        require_once $this->attributes['ajax_path'] . '/class-bingo-card-ajax.php';
        require_once $this->attributes['includes_path'] . '/class-bingo-card-helper.php';
        require_once $this->attributes['public_path'] . '/class-bingo-card-public.php';
    }

    /**
     * Register dependencies
     */
    private function init()
    {
        $this->register_custom_post_types();

        if (BingoCardHelper::is_request('admin')) {
            $admin_obj = new BingoCardAdmin($this->attributes);
            $admin_obj->register_dependencies();
        } elseif (BingoCardHelper::is_request('ajax')) {
            $ajax_obj = new BingoCardAjax($this->attributes);
            $ajax_obj->register_dependencies();
        } elseif (BingoCardHelper::is_request('public')) {
            $public_obj = new BingoCardPublic($this->attributes);
            $public_obj->register_dependencies();
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
//                'taxonomies' => array('category', 'post_tag'),
            )
        );
        if ($result instanceof WP_Error) {
            // Log
            file_put_contents($this->attributes['logs_path'] . '/error.log', '[' . date('Y-m-d H:i:s') . ']' . PHP_EOL . $result->get_error_message() . PHP_EOL, FILE_APPEND);
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
//                'taxonomies' => array('category', 'post_tag'),
            )
        );
        if ($result instanceof WP_Error) {
            // Log
            file_put_contents($this->attributes['logs_path'] . '/error.log', '[' . date('Y-m-d H:i:s') . ']' . PHP_EOL . $result->get_error_message() . PHP_EOL, FILE_APPEND);
        }
    }
}
