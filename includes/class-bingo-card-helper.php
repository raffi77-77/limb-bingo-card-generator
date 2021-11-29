<?php

/**
 * The class the helps with different static methods
 */
class LBCGHelper
{
    /**
     * Google Fonts
     *
     * @var string[]
     */
    public static $fonts = [
        'mochiy-pop-p-one' => [
            'name' => 'Mochiy Pop P One',
            'url' => 'https://fonts.googleapis.com/css2?family=Mochiy+Pop+P+One&display=swap'
        ],
        'dancing-script' => [
            'name' => 'Dancing Script',
            'url' => 'https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap'
        ],
        'saira-condensed' => [
            'name' => 'Saira Condensed',
            'url' => 'https://fonts.googleapis.com/css2?family=Saira+Condensed:wght@100&display=swap'
        ],
        'creepster' => [
            'name' => 'Creepster',
            'url' => 'https://fonts.googleapis.com/css2?family=Creepster&display=swap'
        ],
        'holtwood-one-sc' => [
            'name' => 'Holtwood One SC',
            'url' => 'https://fonts.googleapis.com/css2?family=Holtwood+One+SC&display=swap'
        ],
        'henny-penny' => [
            'name' => 'Henny Penny',
            'url' => 'https://fonts.googleapis.com/css2?family=Henny+Penny&display=swap'
        ]
    ];

    /**
     * Free space word
     *
     * @var string
     */
    public static $free_space_word = '&#9733;';

    public static $default_empty_items = [
        [[4, 5, 2, 7],
        [2, 7, 5, 4],
        [2, 1, 7, 3]],
        [[5, 1, 2, 0],
        [3, 2, 4, 1],
        [8, 3, 6, 0]],
        [[8, 5, 7, 3],
        [8, 0, 4, 1],
        [1, 7, 2, 4]],
        [[7, 3, 6, 0],
        [2, 6, 4, 3],
        [0, 1, 5, 6]],
        [[6, 7, 4, 0],
        [4, 3, 1, 0],
        [4, 0, 3, 6]],
        [[6, 1, 8, 3],
        [5, 3, 8, 7],
        [6, 3, 2, 0]]
    ];

    /**
     * Register custom post types and hooks
     */
    public static function register_custom_post_types()
    {
        self::register_bingo_theme_post_type();
        self::register_bingo_card_post_type();

        add_filter('post_type_link', array('LBCGHelper', 'check_post_link'), 10, 2);
        add_filter('query_vars', array('LBCGHelper', 'query_vars'));
    }

    /**
     * Register custom post types for Bingo Theme
     */
    public static function register_bingo_theme_post_type()
    {
        // Custom post type settings
        $labels = array(
            'name' => __('LBingo themes', 'textdomain'),
            'singular_name' => __('LBingo theme', 'textdomain'),
            'menu_name' => __('LBingo themes', 'textdomain'),
            'name_admin_bar' => __('LBingo theme', 'textdomain'),
            'add_new' => __('Add new', 'textdomain'),
            'add_new_item' => __('Add new bingo theme', 'textdomain'),
            'new_item' => __('New bingo theme', 'textdomain'),
            'edit_item' => __('Edit bingo theme', 'textdomain'),
            'view_item' => __('View bingo theme', 'textdomain'),
            'all_items' => __('All bingo themes', 'textdomain'),
            'search_items' => __('Search LBingo themes', 'textdomain'),
            'not_found' => __('No themes found.', 'textdomain')
        );
        $supports = array('title', 'editor', 'author');
        // Register bingo_card custom post type
        register_post_type('bingo_theme',
            array(
                'labels' => $labels,
                'description' => 'LBingo theme ...',
                'public' => true,
                'publicly_queryable' => true,
                'query_var' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'capability_type' => 'post',
                'has_archive' => true,
                'hierarchical' => true,
                'rewrite' => array('slug' => '/category/%bt-cat%/lbingo-theme'),
                'supports' => $supports,
                'taxonomies' => array('category'),
            )
        );
        add_rewrite_rule(
            'category/([^/]+)/lbingo-theme/([^/]+)/?(([^/]+)/?)?$',
            'index.php?post_type=bingo_theme&name=$matches[2]',
            'top'
        );
    }

    /**
     * Register custom post type for Bingo Card
     */
    public static function register_bingo_card_post_type()
    {
        // Custom post type settings
        $labels = array(
            'name' => __('LBingo cards', 'textdomain'),
            'singular_name' => __('LBingo card', 'textdomain'),
            'menu_name' => __('LBingo cards', 'textdomain'),
            'name_admin_bar' => __('LBingo card', 'textdomain'),
            'add_new' => __('Add new', 'textdomain'),
            'add_new_item' => __('Add new card', 'textdomain'),
            'new_item' => __('New card', 'textdomain'),
            'edit_item' => __('Edit card', 'textdomain'),
            'view_item' => __('View card', 'textdomain'),
            'all_items' => __('All cards', 'textdomain'),
            'search_items' => __('Search LBingo cards', 'textdomain'),
            'not_found' => __('No cards found.', 'textdomain')
        );
        $supports = array('title', 'editor', 'author');
        // Register bingo_card custom post type
        register_post_type('bingo_card',
            array(
                'labels' => $labels,
                'description' => 'LBingo card ...',
                'public' => true,
                'publicly_queryable' => true,
                'query_var' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'capability_type' => 'post',
                'has_archive' => true,
                'hierarchical' => false,
                'rewrite' => array('slug' => 'lbingo-card'),
                'supports' => $supports,
//                'taxonomies' => array('category', 'post_tag'),
            )
        );
        add_rewrite_rule(
            'lbingo-card/([^/]+)/?(([^/]+)/?)?$',
            'index.php?post_type=bingo_card&name=$matches[1]',
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
     * @param object $post
     * @return mixed|void
     */
    public static function check_post_link($post_link, $post = null)
    {
        $post = get_post($post);
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

    /**
     * Get 1-75 bingo card numbers
     *
     * @param bool $random
     * @return array
     */
    public static function get_1_75_bingo_card_numbers($random = false)
    {
        $num_cols = [];
        for ($i = 1; $i < 62; $i += 15) {
            $temp_numbers = range($i, $i + 14);
            if ($random === true) {
                shuffle($temp_numbers);
            }
            $num_cols[] = $temp_numbers;
        }
        $bingo_card_numbers = [];
        $i = 0;
        $j = 0;
        while ($j < 5) {
            $bingo_card_numbers[] = $num_cols[$i][$j];
            ++$i;
            if (($i %= 5) === 0) {
                ++$j;
            }
        }
        return $bingo_card_numbers;
    }

    /**
     * Get 1-90 bingo card numbers
     *
     * @param bool $random
     * @return array
     */
    public static function get_1_90_bingo_card_numbers($random = false)
    {
        $card_numbers = [];
        for ($k = 0; $k < 6; $k++) {
            $single_card_num = [];
            $single_card_num[] = self::get_1_90_card_sub_numbers(1, 9, $random);
            for ($i = 10; $i < 71; $i += 10) {
                $single_card_num[] = self::get_1_90_card_sub_numbers($i, $i + 9, $random);
            }
            $single_card_num[] = self::get_1_90_card_sub_numbers(80, 90, $random);
            $card_numbers[$k] = $single_card_num;
            self::empty_each_line_four_items($card_numbers[$k], $k, $random);
        }
        $bingo_card_numbers = [];
        for ($k = 0; $k < 6; $k++) {
            for ($i = 0; $i < 3; $i++) {
                for ($j = 0; $j < 9; $j++) {
                    $bingo_card_numbers[$k][] = $card_numbers[$k][$j][$i];
                }
            }
        }
        return $bingo_card_numbers;
    }

    /**
     * Get card col numbers
     *
     * @param int $start
     * @param int $end
     * @param bool $random
     * @return array
     */
    public static function get_1_90_card_sub_numbers($start, $end, $random = false)
    {
        $temp_num = range($start, $end);
        if ($random === true) {
            shuffle($temp_num);
        }
        return array_slice($temp_num, 0, 3);
    }

    /**
     * Set empty items in card
     *
     * @param array $card_numbers
     * @param int $index
     * @param bool $random
     */
    public static function empty_each_line_four_items(&$card_numbers, $index, $random = false)
    {
        $temp_indexes = range(0, 8);
        for ($i = 0; $i < 3; $i++) {
            if ($random === true) {
                shuffle($temp_indexes);
                $tmp = array_slice($temp_indexes, 0, 4);
            } else {
                $tmp = self::$default_empty_items[$index][$i];
            }
            foreach ($tmp as $j) {
                $card_numbers[$j][$i] = '';
            }
        }
    }

    /**
     * Get card meta data
     *
     * @param array $data
     * @param bool $from_meta
     * @return array
     */
    public static function collect_card_data_from($data, $from_meta = false)
    {
        $errors = [];
        // Check some cases
        if (empty($data['bingo_card_type']) || ($from_meta && empty($data['bingo_card_type'][0]))) {
            $errors[] = "Bingo card type isn't defined.";
        }
        if (empty($data['bingo_grid_size']) || ($from_meta && empty($data['bingo_grid_size'][0]))) {
            $errors[] = "Bingo card grid size isn't defined.";
        }
        if (($data['bingo_card_type'] !== '1-75' && $data['bingo_card_type'] !== '1-90' && empty($data['bingo_card_content']))) {
            if ($from_meta && ($data['bingo_card_type'][0] !== '1-75' && $data['bingo_card_type'][0] !== '1-90' && empty($data['bingo_card_content'][0]))) {
                $errors[] = "Bingo card words/emojis or numbers are empty.";
            }
        }
        if ((empty($data['bc_header']) || empty($data['bc_grid']) || empty($data['bc_card']))) {
            if ($from_meta && (empty($data['bc_header'][0]) || empty($data['bc_grid'][0]) || empty($data['bc_card'][0]))) {
                $errors[] = "Bingo card styles are not defined.";
            }
        }
        if (!empty($errors)) {
            return [
                'success' => false,
                'data' => $errors
            ];
        }
        // Collect data
        $card_data = [
            'bingo_card_type' => '',
            'bingo_grid_size' => '',
            'bingo_card_title' => '',
            'bingo_card_spec_title' => '',
            'bingo_card_content' => '',
            'bc_header' => '',
            'bc_grid' => '',
            'bc_card' => '',
            'bingo_card_font' => '',
            'bingo_card_free_square' => '',
            'bingo_card_custom_css' => ''
        ];
        foreach ($card_data as $key => $value) {
            if (!empty($data[$key]) || ($from_meta && !empty($data[$key][0]))) {
                $card_data[$key] = $from_meta ? maybe_unserialize($data[$key][0]) : $data[$key];
            }
        }
        return [
            'success' => true,
            'data' => $card_data
        ];
    }

    /**
     * Save bingo card/theme meta fields
     *
     * @param $post_id
     * @param $data
     */
    public static function save_bingo_meta_fields($post_id, $data, $theme_id = null)
    {
        if ($theme_id !== null) {
            $theme_meta_data = get_post_meta($theme_id);
        }
        $special_cards = array('1-75', '1-90');
        // Type and size
        update_post_meta($post_id, 'bingo_card_type', $data['bingo_card_type']);
        update_post_meta($post_id, 'bingo_grid_size', $data['bingo_grid_size']);
        // Title
        if (!empty($data['bingo_card_title'])) {
            $title = trim(wp_strip_all_tags($data['bingo_card_title']));
            update_post_meta($post_id, 'bingo_card_title', $title);
        }
        // 1-75 special title
        if ($data['bingo_card_type'] === '1-75' && !empty($data['bingo_card_spec_title']) && count($data['bingo_card_spec_title']) === 5) {
            update_post_meta($post_id, 'bingo_card_spec_title', implode('|', $data['bingo_card_spec_title']));
        }
        // Words/emojis or numbers
        if (!in_array($data['bingo_card_type'], $special_cards) && !empty($data['bingo_card_content'])) {
            update_post_meta($post_id, 'bingo_card_content', trim(wp_strip_all_tags($data['bingo_card_content'])));
        }
        // Header color, image with attributes
        if (!empty($data['bc_header'])) {
            if (!empty($_FILES['bc_header']['size']['image'])) {
                $attach_id = self::upload_attachment($_FILES['bc_header'], $post_id);
                $data['bc_header']['image'] = $attach_id;
            } elseif (!empty($theme_meta_data['bc_header'][0])) {
                $bc_header = unserialize($theme_meta_data['bc_header'][0]);
                $data['bc_header']['image'] = $bc_header['image'];
            } elseif (empty($data['bc_header']['image'])) {
                $data['bc_header']['image'] = '0';
            }
            if (empty($data['bc_header']['repeat'])) {
                $data['bc_header']['repeat'] = 'no-repeat';
            }
            if (isset($data['bc_header']['remove_image']) && (int)$data['bc_header']['remove_image'] === 1) {
                $data['bc_header']['image'] = '0';
            }
            update_post_meta($post_id, 'bc_header', $data['bc_header']);
        }
        // Grid color, image with attributes
        if (!empty($data['bc_grid'])) {
            if (!empty($_FILES['bc_grid']['size']['image'])) {
                $attach_id = self::upload_attachment($_FILES['bc_grid'], $post_id);
                $data['bc_grid']['image'] = $attach_id;
            } elseif (!empty($theme_meta_data['bc_grid'][0])) {
                $bc_grid = unserialize($theme_meta_data['bc_grid'][0]);
                $data['bc_grid']['image'] = $bc_grid['image'];
            } elseif (empty($data['bc_header']['image'])) {
                $data['bc_grid']['image'] = '0';
            }
            if (empty($data['bc_grid']['repeat'])) {
                $data['bc_grid']['repeat'] = 'no-repeat';
            }
            if (isset($data['bc_grid']['remove_image']) && (int)$data['bc_grid']['remove_image'] === 1) {
                $data['bc_grid']['image'] = '0';
            }
            update_post_meta($post_id, 'bc_grid', $data['bc_grid']);
        }
        // Card color, image with attributes
        if (!empty($data['bc_card'])) {
            if (!empty($_FILES['bc_card']['size']['image'])) {
                $attach_id = self::upload_attachment($_FILES['bc_card'], $post_id);
                $data['bc_card']['image'] = $attach_id;
            } elseif (!empty($theme_meta_data['bc_card'][0])) {
                $bc_card = unserialize($theme_meta_data['bc_card'][0]);
                $data['bc_card']['image'] = $bc_card['image'];
            } elseif (empty($data['bc_header']['image'])) {
                $data['bc_card']['image'] = 0;
            }
            if (empty($data['bc_card']['repeat'])) {
                $data['bc_card']['repeat'] = 'no-repeat';
            }
            if (isset($data['bc_card']['remove_image']) && (int)$data['bc_card']['remove_image'] === 1) {
                $data['bc_card']['image'] = '0';
            }
            update_post_meta($post_id, 'bc_card', $data['bc_card']);
        }
        // Font
        if (!empty($data['bingo_card_font'])) {
            update_post_meta($post_id, 'bingo_card_font', $data['bingo_card_font']);
        }
        // Free square
        update_post_meta($post_id, 'bingo_card_free_square', empty($data['bingo_card_free_square']) ? 'off' : 'on');
        // Custom CSS
        if (!empty($data['bingo_card_custom_css'])) {
            update_post_meta($post_id, 'bingo_card_custom_css', trim(wp_strip_all_tags($data['bingo_card_custom_css'])));
        }
    }

    /**
     * Check if correct emails
     *
     * @param string|array $emails
     * @return bool
     */
    public static function is_valid_emails($emails)
    {
        if (!is_array($emails)) {
            $emails = [$emails];
        }
        foreach ($emails as $email) {
            if (!is_email($email)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Invite emails
     *
     * @param $bingo_card_id
     * @param $author_email
     * @param $invite_emails
     * @return array
     */
    public static function invite_emails($bingo_card_id, $author_email, $invite_emails)
    {
        $data = get_post_meta($bingo_card_id);
        // Save card content
        if ($data['bingo_card_type'][0] === '1-75') {
            $content_words = self::get_1_75_bingo_card_numbers(true);
        } elseif ($data['bingo_card_type'][0] === '1-90') {
            $content_words = self::get_1_90_bingo_card_numbers(true);
            foreach ($content_words as $key => $value) {
                $content_words[$key] = implode(';', $value);
            }
        } else {
            $content_words = explode("\r\n", $data['bingo_card_content'][0]);
            shuffle($content_words);
        }
        update_post_meta($bingo_card_id, 'bingo_card_own_content', implode($data['bingo_card_type'][0] === '1-90' ? ':' : "\r\n", $content_words));
        update_post_meta($bingo_card_id, 'author_email', $author_email);
        // Send email
        $subject = "Your Bingo Card";
        // Get email content
        $email_content = self::get_new_bingo_email_content($subject, $author_email, $bingo_card_id);
        $sent = mail($author_email, $subject, $email_content, ['Content-Type: text/html; charset=UTF-8']);
        if (!$sent) {
            return [
                'success' => false,
                'errors' => ["Invitation fail. Failed to email your card."],
                'failed_invites' => []
            ];
        }
        // Invite
        $failed_to_invite = [];
        $invite_subject = "Get Your New Bingo Card";
        foreach ($invite_emails as $user_email) {
            // Create new child bingo card
            $new_bc_id = self::create_child_bingo_card($bingo_card_id, $data, $user_email);
            if ($new_bc_id === false) {
                $failed_to_invite[] = $user_email;
                continue;
            }
            // Get email content
            $email_content = self::get_new_bingo_email_content($invite_subject, $user_email, $new_bc_id);
            $sent = mail($user_email, $invite_subject, $email_content, ['Content-Type: text/html; charset=UTF-8']);
            if (!$sent) {
                // Delete not used bingo card
                wp_delete_post($new_bc_id);
                $failed_to_invite[] = $user_email;
            }
        }
        return [
            'success' => true,
            'errors' => [],
            'failed_invites' => $failed_to_invite
        ];
    }

    /**
     * Get new bingo card email content
     *
     * @param $title
     * @param $user_id
     * @param $user_email
     * @param $bc_id
     * @return false|string
     */
    public static function get_new_bingo_email_content($title, $user_email, $bc_id)
    {
        // Get bingo card link
        $bc_link = get_permalink($bc_id);
        /**
         * Get email content
         * Necessary variables for email template
         *
         * string   $title       Email subject
         * string   $user_email  User email
         * int      $bc_id       Bingo card id
         * string   $bc_link     Bingo card link
         */
        ob_start();
        include 'templates/email-new-bingo-card-template.php';
        $content = ob_get_clean();
        return $content;
    }

    /**
     * Get user password reset link
     *
     * @param $user_id
     * @return string
     */
    public static function get_password_reset_link($user_id)
    {
        try {
            $user = get_user_by('id', $user_id);
            if ($user instanceof WP_User) {
                $rp_key = get_password_reset_key($user);
                if (!is_wp_error($rp_key)) {
                    return network_site_url("wp-login.php?action=rp&key=$rp_key&login=" . rawurlencode($user->user_login), 'login');
                }
            }
            return '';
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Create child bingo card
     *
     * @param $parent_bc_id
     * @param $parent_bc_meta_data
     * @param $user_email
     * @return false|int
     */
    public static function create_child_bingo_card($parent_bc_id, $parent_bc_meta_data, $user_email)
    {
        // Collect data
        $result = self::collect_card_data_from($parent_bc_meta_data, true);
        if ($result['success'] === false) {
            return false;
        }
        // Create bingo card post
        $bc_result = self::insert_bingo_card($result['data'], 'publish');
        if ($bc_result === false) {
            return false;
        }
        // Save card data
        self::save_bingo_meta_fields($bc_result['id'], $result['data']);
        // Save card content
        if ($result['data']['bingo_card_type'] === '1-75') {
            $content_words = self::get_1_75_bingo_card_numbers(true);
        } elseif ($result['data']['bingo_card_type'] === '1-90') {
            $content_words = self::get_1_90_bingo_card_numbers(true);
            foreach ($content_words as $key => $value) {
                $content_words[$key] = implode(';', $value);
            }
        } else {
            $content_words = explode("\r\n", $result['data']['bingo_card_content']);
            shuffle($content_words);
        }
        update_post_meta($bc_result['id'], 'bingo_card_own_content', implode($result['data']['bingo_card_type'] === '1-90' ? ':' : "\r\n", $content_words));
        update_post_meta($bc_result['id'], 'parent_bingo_card_id', $parent_bc_id);
        update_post_meta($bc_result['id'], 'author_email', $user_email);
        return $bc_result['id'];
    }

    /**
     * Insert bingo card
     *
     * @param $data
     * @return array|false
     */
    public static function insert_bingo_card($data, $status = 'draft')
    {
        $title = "Bingo Card {$data['bingo_card_type']} {$data['bingo_grid_size']}";
        $uniq_string = wp_generate_password(16, false);
//        $uniq_string = wp_generate_uuid4();
//        $uniq_string = str_replace('-', '', $uniq_string);
        // Create new card
        $args = [
            'post_author' => 0,
            'post_title' => $title,
            'post_type' => 'bingo_card',
            'post_name' => $uniq_string,
            'post_status' => $status
        ];
        $id = wp_insert_post($args);
        if (is_wp_error($id) || $id === 0) {
            return false;
        }
        return [
            'id' => $id,
            'uniq_id' => $uniq_string
        ];
    }

    /**
     * Generate all contents
     *
     * @param int $post_id
     * @param int $count
     * @return array
     */
    public static function generate_all_content_info($post_id, $count, $wanted_count)
    {
        $data = get_post_meta($post_id);
        if (!empty($data['all_content'][0])) {
            return explode('|', $data['all_content'][0]);
        }
        $all = [];
        if ($data['bingo_card_type'][0] === '1-75') {
            for ($i = 0; $i < $count; $i++) {
                $content_items = self::get_1_75_bingo_card_numbers(true);
                $all[] = implode(';', $content_items);
            }
        } elseif ($data['bingo_card_type'][0] === '1-90') {
            for ($i = 0; $i < $count; $i++) {
                $content_items = self::get_1_90_bingo_card_numbers(true);
                foreach ($content_items as $key => $value) {
                    $content_items[$key] = implode(';', $value);
                }
                $all[] = implode(':', $content_items);
            }
        } else {
            $items_count = $data['bingo_grid_size'][0][0] ** 2;
            $content_items = explode("\r\n", $data['bingo_card_content'][0]);
            $indexes = array_keys($content_items);
            for ($i = 0; $i < $count; $i++) {
                shuffle($indexes);
                $all[] = implode(';', array_slice($indexes, 0, $items_count));
            }
        }
        update_post_meta($post_id, 'all_content', implode('|', $all));
        return array_slice($all, 0, $wanted_count);
    }

    /**
     * Upload file
     *
     * @param $file
     * @param $post_id
     * @return int|WP_Error
     */
    public static function upload_attachment($file, $post_id)
    {
        $upload_id = 0;
        $wp_upload_dir = wp_upload_dir();
        $new_file_path = $wp_upload_dir['path'] . '/' . $file['name']['image'];
        $new_file_mime = mime_content_type($file['tmp_name']['image']);
        $i = 1;
        while (file_exists($new_file_path)) {
            $i++;
            $new_file_path = $wp_upload_dir['path'] . '/' . $i . '_' . $file['name']['image'];
        }
        if (move_uploaded_file($file['tmp_name']['image'], $new_file_path)) {
            $upload_id = wp_insert_attachment(array(
                'guid' => $new_file_path,
                'post_mime_type' => $new_file_mime,
                'post_title' => preg_replace('/\.[^.]+$/', '', $file['name']['image']),
                'post_content' => '',
                'post_status' => 'inherit'
            ), $new_file_path);
            wp_update_attachment_metadata($upload_id, wp_generate_attachment_metadata($upload_id, $new_file_path));
        }
        if (is_wp_error($upload_id)) {
            $upload_id = 0;
        }
        return $upload_id;
    }
}