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
        BingoCardHelper::register_bingo_theme_post_type();
        BingoCardHelper::register_bingo_card_post_type();
//        BingoCardHelper::register_bingo_theme_link();

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
}
