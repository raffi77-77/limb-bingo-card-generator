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
    }

    /**
     * Generate card
     */
    public function bingo_card_generation()
    {
        print_r(json_encode([
            'success' => true,
            'errors' => []
        ]));
        die();
    }
}