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
        $this->enqueue_scripts_and_styles();
    }

    /**
     * Enqueue public scripts and styles
     */
    public function enqueue_scripts_and_styles() {
//        global $post_type;
//        if ('bingo_theme' === $post_type) {
        wp_enqueue_style('limb-bingo-card-generator', $this->attributes['plugin_url'] . '/public/css/limb-binco-card-generator.css?ver=' . BingoCard::VERSION);
//        }
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
            $single_template = $this->attributes['templates_path'] . '/bingo-theme-template.php';
        }
        return $single_template;
    }
}