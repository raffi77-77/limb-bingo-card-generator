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
        add_action('wp_ajax_nopriv_bingo_card_generation', array($this, 'bingo_card_generation'));
        add_action('wp_ajax_bingo_card_generation', array($this, 'bingo_card_generation'));
        add_action('wp_ajax_nopriv_bc_invitation', array($this, 'bingo_card_invitation'));
        add_action('wp_ajax_bc_invitation', array($this, 'bingo_card_invitation'));
    }

    /**
     * Generate author card
     */
    public function bingo_card_generation()
    {
        // Check current bingo theme
        global $post;
        if (empty($post->post_type) && $post->post_type !== 'bingo_theme') {
            print_r(json_encode([
                'success' => false,
                'errors' => ["Please, generate bingo card from any bingo theme page."],
                'cardId' => 0
            ]));
            die();
        }
        // Create card
        $data = BingoCardHelper::collect_card_data_from($_POST);
        $title = "Bingo Card {$data['bingo_card_type']} {$data['bingo_grid_size']}";
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
            print_r(json_encode([
                'success' => false,
                'errors' => ["Something went wrong. Please try again."],
                'cardId' => 0
            ]));
            die();
        }
        // TODO continue
        BingoCardHelper::save_card_meta_fields($id, $data);
        print_r(json_encode([
            'success' => true,
            'errors' => [],
            'cardId' => $id
        ]));
        die();
    }

    public function bingo_card_invitation() {
        // Check current bingo theme
//        if () TODO
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
                'errors' => $error_messages
            ]));
            die();
        }
        // Create bingo cards TODO
        BingoCardHelper::create_bingo_cards((int)$_GET['c'], $author_email, $invite_emails);
        print_r(json_encode([
            'success' => true,
            'errors' => []
        ]));
        die();
    }
}