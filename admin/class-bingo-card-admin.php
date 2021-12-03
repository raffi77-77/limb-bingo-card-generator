<?php

/**
 * The admin-specific functionality of the plugin
 */
class LBCGAdmin
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
        add_action('save_post', array($this, 'save_custom_fields'), 10, 3);
        add_action('post_updated_messages', array($this, 'show_editor_message'));
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
     * @param int $post_id
     * @param WP_Post $post
     * @param bool $update
     */
    public function save_custom_fields($post_id, $post, $update)
    {
        if ($post->post_type === 'bingo_theme' || $post->post_type === 'bingo_card') {
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
        if (!empty($_POST['lbcg_action']) && $_POST['lbcg_action'] === 'save_bc_post') {
            LBCGHelper::save_bingo_meta_fields($post_id, $_POST);
        }
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
        if (($post->post_type === 'bingo_theme' || $post->post_type === 'bingo_card') && $post->post_status !== 'auto-draft') {
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
            wp_enqueue_script('lbcg-vanilla-js', $this->attributes['plugin_url'] . 'includes/js/vanilla.js');
            if (!did_action('wp_enqueue_media')) {
                wp_enqueue_media();
            }
            wp_enqueue_script('lbcg-bingo-theme-admin-script', $this->attributes['plugin_url'] . 'admin/js/bingo-theme.js', [], LBCG::VERSION);
            wp_enqueue_style('lbcg-bingo-theme-admin-style', $this->attributes['plugin_url'] . 'admin/css/bingo-theme.css', [], LBCG::VERSION);
        }
    }
}