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
            wp_enqueue_script('lbcg-vanilla-js', $this->attributes['plugin_url'] . 'includes/js/vanilla.js');
            /*if (!did_action('wp_enqueue_media')) {
                wp_enqueue_media();
            }*/
            wp_enqueue_script('lbcg-bingo-card-generator-js', $this->attributes['plugin_url'] . 'public/js/lbcg-bingo-card-generator.js?ver=' . BingoCard::VERSION);
            wp_localize_script('lbcg-bingo-card-generator-js', 'LBCG', [
                'fonts' => BingoCardHelper::$fonts,
                'freeSquareWord' => BingoCardHelper::$free_space_word,
                'ajaxUrl' => admin_url('admin-ajax.php')
            ]);
            wp_enqueue_style('lbcg-bingo-card-generator-css', $this->attributes['plugin_url'] . 'public/css/lbcg-binco-card-generator.min.css?ver=' . BingoCard::VERSION);
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
        if ($post_type === 'bingo_theme') {
            if (strpos($_SERVER['REQUEST_URI'], '/invitation/') !== false) {
                $single_template = $this->attributes['templates_path'] . '/invitation-template.php';
            } else {
                $single_template = $this->attributes['templates_path'] . '/bingo-theme-template.php';
            }
        } else if ($post_type === 'bingo_card') {
            if (strpos($_SERVER['REQUEST_URI'], '/all/')) {
                $single_template = $this->attributes['templates_path'] . '/all-bingo-cards-template.php';
            } else {
                $single_template = $this->attributes['templates_path'] . '/bingo-card-template.php';
            }
        }
        return $single_template;
    }

    /**
     * Add custom css
     */
    public function add_custom_css()
    {
        ?>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <?php foreach (BingoCardHelper::$fonts as $font): ?>
        <link href="<?php echo $font['url'] ?>" rel="stylesheet">
    <?php endforeach; ?>
        <?php
        global $post;
        if ($post instanceof WP_Post && ($post->post_type === 'bingo_theme' || $post->post_type === 'bingo_card')) {
            $custom_css = get_post_meta($post->ID, 'bingo_card_custom_css', true);
            ?>
            <style type="text/css">
                <?php echo trim(wp_strip_all_tags($custom_css)); ?>
            </style>
            <?php
        }
    }
}