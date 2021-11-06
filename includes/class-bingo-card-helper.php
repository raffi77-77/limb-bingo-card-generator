<?php

/**
 * The class the helps with different static methods
 */
class BingoCardHelper
{
    /**
     * Register custom post types for Bingo Theme
     */
    public static function register_bingo_theme_post_type()
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
                'rewrite' => array('slug' => 'bingo-theme', 'with_front' => false),
                'supports' => $supports,
                'taxonomies' => array('category'),
            )
        );
        /*if ($result instanceof WP_Error) {
            // Log
            file_put_contents($this->attributes['logs_path'] . '/error.log', '[' . date('Y-m-d H:i:s') . ']' . PHP_EOL . $result->get_error_message() . PHP_EOL, FILE_APPEND);
        }*/
    }

    /**
     * Register custom post type for Bingo Card
     */
    public static function register_bingo_card_post_type()
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
        /*if ($result instanceof WP_Error) {
            // Log
            file_put_contents($this->attributes['logs_path'] . '/error.log', '[' . date('Y-m-d H:i:s') . ']' . PHP_EOL . $result->get_error_message() . PHP_EOL, FILE_APPEND);
        }*/
    }

    public static function register_bingo_theme_link()
    {
        add_action('init', array('BingoCardHelper', 'add_rewrite_rules'));
        add_filter('query_vars', array('BingoCardHelper', 'query_vars'));
        add_filter('post_type_link', array('BingoCardHelper', 'check_post_link'), 1, 2);
    }

    /**
     * Add rewrite rules
     */
    public static function add_rewrite_rules() {
        add_rewrite_rule(
            '([^/]+)/bingo-theme/([^/]+)/?$',
            'index.php?bingo_theme=$matches[1]',
            'top'
        );
    }

    /**
     * Add bingo theme category to query
     *
     * @param $query_vars
     * @return mixed
     */
    public static function query_vars($query_vars)
    {
        $query_vars[] = 'bt-cat';
        return $query_vars;
    }

    /**
     * Check post link
     *
     * @param $post_link
     * @param int $post_id
     * @return string
     */
    public static function check_post_link($post_link, $post_id = 0)
    {
        $post = get_post($post_id);
        if ($post instanceof WP_Post && $post->post_type == 'bingo_theme') {
            $terms = wp_get_object_terms($post->ID, 'category');
            if ($terms) {
                foreach ($terms as $term) {
                    if (0 == $term->parent) {
                        return str_replace('%bt-cat%', $term->slug, $post_link);
                    }
                }
            } else {
                return str_replace('%bt-cat%', 'uncategorized', $post_link);
            }
        }
        return $post_link;
    }

    /**
     * Checks weather the request comes from admin|ajax|cron|public
     *
     * @param null|string $type
     * @return bool
     */
    public static function is_request($type = null)
    {
        $is_ajax = (defined('DOING_AJAX') && DOING_AJAX);
        switch ($type) {
            case 'admin' :
                return is_admin() && !$is_ajax;
            case 'ajax' :
                return $is_ajax;
            case 'cron' :
                return (defined('DOING_CRON') && DOING_CRON);
            case 'public' :
                return (!is_admin() && !$is_ajax);
        }

        return false;
    }

    /**
     * Get bingo card default content
     *
     * @param string $type
     * @param string $size
     * @return array
     */
    public static function get_bg_default_content($type, $size)
    {
        if (empty($type) || empty($size)) {
            $type = '1-9';
            $size = '3x3';
        }
        if ($size === '3x3') {
            $to = 36;
        } elseif ($size === '4x4') {
            $to = 64;
        } else {
            $to = 100;
        }
        return [
            'words_count' => $to,
            'words' => implode("\n", range(1, $to))
        ];
    }
}