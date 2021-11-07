<?php

/**
 * The public-facing functionality of the plugin
 */
class BingoCardPublic
{
    /**
     * Plugin all needed properties in one place
     *
     * @var array $attributes The array containing main attributes of the plugin
     */
    protected $attributes = [];

    /**
     * Construct Bingo Card Public object
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
        add_filter('single_template', array($this, 'get_custom_post_type_template'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts_and_styles'), 10);
        add_action('wp_head', array($this, 'add_custom_css'));
    }

    /**
     * Enqueue public scripts and styles
     */
    public function enqueue_scripts_and_styles()
    {
        if (is_singular('bingo_theme') || is_singular('bingo_card')) {
            wp_enqueue_script('jquery');
            wp_enqueue_script('limb-bingo-card-generator-js', $this->attributes['plugin_url'] . '/public/js/limb-bingo-card-generator.js?ver=' . BingoCard::VERSION);
            wp_enqueue_style('limb-bingo-card-generator-css', $this->attributes['plugin_url'] . '/public/css/limb-binco-card-generator.min.css?ver=' . BingoCard::VERSION);
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
        global $post_type;
        if ($post_type === 'bingo_theme' || $post_type === 'bingo_card') {
            $single_template = $this->attributes['templates_path'] . '/bingo-theme-template.php';
        }
        return $single_template;
    }

    /**
     * Add custom css
     */
    public function add_custom_css()
    {
        global $post;
        if ($post->post_type === 'bingo_theme' || $post->post_type === 'bingo_card') {
            $custom_css = get_post_meta($post->ID, 'bingo_card_custom_css', true);
            ?>
            <style type="text/css">
                <?php echo $custom_css; ?>
            </style>
            <?php
        }
    }
}