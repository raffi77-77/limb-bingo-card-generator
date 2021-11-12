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
        add_action('post_updated_messages', array($this, 'show_editor_message'));
        add_action('admin_print_scripts-post-new.php', array($this, 'enqueue_admin_script_and_styles'), 11);
        add_action('admin_print_scripts-post.php', array($this, 'enqueue_admin_script_and_styles'), 11);
        add_action('admin_post_nopriv_bingo_card_generation', array($this, 'bingo_card_generation'));
        add_action('admin_post_bingo_card_generation', array($this, 'bingo_card_generation'));
    }

    /**
     * Generate author card
     */
    public function bingo_card_generation()
    {
        // Check current bingo theme
        $post = get_post($_POST['bingo_theme_id']);
        if (empty($post->post_type) || $post->post_type !== 'bingo_theme') {
            return;
        }
        // Create card
        $result = BingoCardHelper::collect_card_data_from($_POST);
        if ($result['success'] === false) {
            return;
        }
        $title = "Bingo Card {$result['data']['bingo_card_type']} {$result['data']['bingo_grid_size']}";
        $uniq_string = wp_generate_password(16, false);
        // Create new card
        $args = [
            'post_author' => 0,
            'post_title' => $title,
            'post_type' => 'bingo_card',
            'post_name' => str_replace(' ', '-', strtolower($title)) . '-' . $uniq_string
        ];
        $id = wp_insert_post($args);
        if ($id instanceof WP_Error || $id === 0) {
            return;
        }
        // Save card data
        BingoCardHelper::save_bingo_meta_fields($id, $result['data'], $_POST['bingo_theme_id']);
        wp_safe_redirect(get_permalink($_POST['bingo_theme_id']) . 'invitation?bt=' . $_POST['bingo_theme_id'] . '&bc=' . $id);
        die();
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
            $this->save_bingo_custom_fields($post_id);
        }
    }

    /**
     * Save bingo theme custom fields
     *
     * @param $post_id
     */
    private function save_bingo_custom_fields($post_id)
    {
        BingoCardHelper::save_bingo_meta_fields($post_id, $_POST);
    }

    /**
     * Set error messages
     *
     * @param array $messages
     * @return array
     */
    public function show_editor_message($messages)
    {
        global $post;
        if ($post->post_type === 'bingo_theme' || $post->post_type === 'bingo_card') {
            $post_meta = get_post_meta($post->ID);
            // Collecting errors
            $errors = [];
            if (empty($post_meta['bingo_card_type'][0])) {
                $errors[] = "Bingo card type isn't defined.";
            }
            if (empty($post_meta['bingo_grid_size'][0])) {
                $errors[] = "Bingo card grid size isn't defined.";
            }
            // Setting errors to show
            if (!empty($errors)) {
                foreach ($errors as $key => $error) {
                    $unique = 'bc_error_' . ($key + 1);
                    add_settings_error($unique, str_replace('_', '-', $unique), $error);
                    settings_errors($unique);
                }
            }
        }
        return $messages;
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