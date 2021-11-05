<?php

/**
 * The admin-specific functionality of the plugin
 */
class BingoCardAdmin
{
    /**
     * Plugin all needed properties in one place
     *
     * @var array $attributes The array containing main attributes of the plugin
     */
    protected $attributes = [];

    /**
     * Construct Bingo Card Admin object
     *
     * @param $attributes
     */
    public function __construct($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Add actions, filters ...
     */
    public function register_dependencies()
    {
        add_action('add_meta_boxes', array($this, 'add_custom_meta_boxes'));
        add_action('save_post', array($this, 'save_custom_fields'));
        add_action('admin_print_scripts-post-new.php', array($this, 'enqueue_admin_script_and_styles'), 11);
        add_action('admin_print_scripts-post.php', array($this, 'enqueue_admin_script_and_styles'), 11);
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
     * Get bingo theme custom fields template
     */
    public function get_bingo_theme_custom_fields_template()
    {
        include($this->attributes['admin_templates_path'] . '/bingo-theme-custom-fields-template.php');
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

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_script_and_styles()
    {
        global $post_type;
        if ('bingo_theme' === $post_type) {
            wp_enqueue_script('jquery-3.6.0', $this->attributes['plugin_url'] . '/admin/js/jquery-3.6.0.min.js');
            wp_enqueue_script('bingo-theme-admin-script', $this->attributes['plugin_url'] . '/admin/js/bingo-theme.js?ver=' . BingoCard::VERSION);
            wp_enqueue_style('bingo-theme-admin-style', $this->attributes['plugin_url'] . '/admin/css/bingo-theme.css?ver=' . BingoCard::VERSION);
        }
    }
}