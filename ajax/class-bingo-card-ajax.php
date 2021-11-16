<?php

/**
 * The ajax related functionality of the plugin
 */
class BingoCardAjax
{
    /**
     * Plugin all needed properties in one place
     *
     * @var array $attributes The array containing main attributes of the plugin
     */
    protected $attributes = [];

    /**
     * Construct Bingo Card Ajax object
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
        add_action('wp_ajax_nopriv_lbcg_bc_generation', array($this, 'generation'));
        add_action('wp_ajax_lbcg_bc_generation', array($this, 'generation'));
        add_action('wp_ajax_nopriv_lbcg_bc_invitation', array($this, 'invitation'));
        add_action('wp_ajax_lbcg_bc_invitation', array($this, 'invitation'));
    }

    /**
     * Generate author card
     */
    public function generation()
    {
        // Check current bingo theme
        $post_type = get_post_type($_POST['bingo_theme_id']);
        if (empty($post_type) || $post_type !== 'bingo_theme') {
            print_r(json_encode([
                'success' => false,
                'errors' => ["Invalid request"],
                'redirectTo' => get_site_url()
            ]));
            die();
        }
        // Create card
        $result = BingoCardHelper::collect_card_data_from($_POST);
        if ($result['success'] === false) {
            print_r(json_encode([
                'success' => false,
                'errors' => $result['data'],
                'redirectTo' => ''
            ]));
            die();
        }
        $bc_result = BingoCardHelper::insert_bingo_card($result['data']);
        if ($bc_result === false) {
            print_r(json_encode([
                'success' => false,
                'errors' => ["Failed to save data. Please try again."],
                'redirectTo' => ''
            ]));
            die();
        }
        // Save card data
        BingoCardHelper::save_bingo_meta_fields($bc_result['id'], $result['data'], $_POST['bingo_theme_id']);
        update_post_meta($bc_result['id'], 'bingo_theme_id', $_POST['bingo_theme_id']);
        print_r(json_encode([
            'success' => true,
            'errors' => [],
            'redirectTo' => get_permalink($_POST['bingo_theme_id']) . 'invitation/?bc=' . $bc_result['uniq_id']
        ]));
        die();
    }

    /**
     * Create all cards and invite them
     */
    public function invitation()
    {
        // Check current parent bingo card
        if (empty($_GET['bc'])) {
            print_r(json_encode([
                'success' => false,
                'errors' => ["Invalid request."],
                'redirectTo' => ''
            ]));
            die();
        }
        $bc_posts = get_posts([
            'name' => $_GET['bc'],
            'post_type' => 'bingo_card',
            'posts_per_page' => 1,
            'post_status' => 'publish'//'draft',
        ]);
        if (empty($bc_posts[0]->ID)) {
            print_r(json_encode([
                'success' => false,
                'errors' => ["Invalid request."],
                'redirectTo' => ''
            ]));
            die();
        }
        $bingo_card = $bc_posts[0];
        // Get emails
        $author_email = trim($_POST['author_email']);
        $invite_emails = explode("\r\n", $_POST['invite_emails']);
        $invite_emails = array_map('trim', $invite_emails);
        $invite_emails = array_unique($invite_emails);
        // Remove author email from invite emails list
        if ($key = array_search($author_email, $invite_emails) !== false) {
            unset($invite_emails[$key]);
        }
        // Check email validations
        if (!BingoCardHelper::is_valid_emails($author_email)) {
            $error_messages[] = "Your email is not valid. Please enter correct email.";
        }
        if (!BingoCardHelper::is_valid_emails($invite_emails)) {
            $error_messages[] = "Please check invitation emails validation and try again.";
        }
        if (!empty($error_messages)) {
            print_r(json_encode([
                'success' => false,
                'errors' => $error_messages,
                'redirectTo' => ''
            ]));
            die();
        }
        // Create bingo cards
        $result = BingoCardHelper::invite_users($bingo_card->ID, $author_email, $invite_emails);
        if ($result['success'] === false) {
            print_r(json_encode([
                'success' => false,
                'errors' => $result['errors'],
                'failed_invites' => $result['failed_invites'],
                'redirectTo' => get_permalink($bingo_card->ID) . 'all'
            ]));
            die();
        }
        print_r(json_encode([
            'success' => true,
            'errors' => [],
            'failed_invites' => $result['failed_invites'],
            'redirectTo' => get_permalink($bingo_card->ID) . 'all'
        ]));
        die();
    }
}