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
            'Custom Fields (set up default values)',
            array($this, 'get_bingo_theme_custom_fields_template'),
            'bingo_theme'
        );
        add_meta_box(
            'bingo-theme-custom-fields',
            'Custom Fields',
            array($this, 'get_bingo_card_custom_fields_template'),
            'bingo_card'
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
     * Get bingo card custom fields template
     */
    public function get_bingo_card_custom_fields_template()
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
        if ($post_type === 'bingo_theme' || $post_type === 'bingo_card') {
            $this->save_theme_custom_fields($post_id);
        }
    }

    /**
     * Save bingo theme custom fields
     *
     * @param $post_id
     */
    private function save_theme_custom_fields($post_id)
    {
        // Type and size
        if (empty($_POST['bingo_card_type']) || empty($_POST['bingo_grid_size'])) {
            // TODO bingo card type and size aren't defined
            return;
        }
        $special_cards = array('1-75', '1-90');
        update_post_meta($post_id, 'bingo_card_type', $_POST['bingo_card_type']);
        update_post_meta($post_id, 'bingo_grid_size', $_POST['bingo_grid_size']);
        $data = get_post_meta($post_id);
        // Title
        if (!empty($_POST['bingo_card_title'])) {
            // $title = trim(str_replace("\r\n", '<br>', wp_strip_all_tags($_POST['bingo_card_title'])));
            $title = trim(wp_strip_all_tags($_POST['bingo_card_title']));
            update_post_meta($post_id, 'bingo_card_title', $title);
        }
        // 1-75 special title
        if ($_POST['bingo_card_type'] === '1-75' && !empty($_POST['bingo_card_spec_title']) && count($_POST['bingo_card_spec_title']) === 5) {
            update_post_meta($post_id, 'bingo_card_spec_title', implode('|', $_POST['bingo_card_spec_title']));
        } else {
            delete_post_meta($post_id, 'bingo_card_spec_title');
        }
        // Words/emojis or numbers
        if (!in_array($_POST['bingo_card_type'], $special_cards) && !empty($_POST['bingo_card_content'])) {
            update_post_meta($post_id, 'bingo_card_content', trim(wp_strip_all_tags($_POST['bingo_card_content'])));
        } else {
            delete_post_meta($post_id, 'bingo_card_content');
        }
        // Header color, image with attributes
        if (!empty($_POST['bc_header'])) {
            if (empty($_POST['bc_header']['repeat'])) {
                $_POST['bc_header']['repeat'] = 'off';
            }
            update_post_meta($post_id, 'bc_header', $_POST['bc_header']);
        }
        // Grid color, image with attributes
        if (!empty($_POST['bc_grid'])) {
            if (empty($_POST['bc_grid']['repeat'])) {
                $_POST['bc_grid']['repeat'] = 'off';
            }
            update_post_meta($post_id, 'bc_grid', $_POST['bc_grid']);
        }
        // Card color, image with attributes
        if (!empty($_POST['bc_card'])) {
            if (empty($_POST['bc_card']['repeat'])) {
                $_POST['bc_card']['repeat'] = 'off';
            }
            update_post_meta($post_id, 'bc_card', $_POST['bc_card']);
        }
        // Font
        if (!empty($_POST['bingo_card_font'])) {
            update_post_meta($post_id, 'bingo_card_font', $_POST['bingo_card_font']);
        }
        // Free square
        update_post_meta($post_id, 'bingo_card_free_square', empty($_POST['bingo_card_free_square']) ? 'off' : 'on');
        // Custom CSS
        if (!empty($_POST['bingo_card_custom_css'])) {
            update_post_meta($post_id, 'bingo_card_custom_css', trim(wp_strip_all_tags($_POST['bingo_card_custom_css'])));
        }
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_script_and_styles()
    {
        global $post_type;
        if ($post_type === 'bingo_theme' || $post_type === 'bingo_card') {
            wp_enqueue_script('jquery');
            if (!did_action('wp_enqueue_media')) {
                wp_enqueue_media();
            }
            wp_enqueue_script('bingo-theme-admin-script', $this->attributes['plugin_url'] . '/admin/js/bingo-theme.js?ver=' . BingoCard::VERSION);
            wp_enqueue_style('bingo-theme-admin-style', $this->attributes['plugin_url'] . '/admin/css/bingo-theme.css?ver=' . BingoCard::VERSION);
        }
    }
}