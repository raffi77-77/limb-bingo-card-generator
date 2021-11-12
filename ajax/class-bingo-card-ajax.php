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
        add_action('wp_ajax_nopriv_bc_invitation', array($this, 'bingo_card_invitation'));
        add_action('wp_ajax_bc_invitation', array($this, 'bingo_card_invitation'));
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
        BingoCardHelper::create_bingo_cards((int)$_GET['bc'], $author_email, $invite_emails);
        print_r(json_encode([
            'success' => true,
            'errors' => []
        ]));
        die();
    }
}