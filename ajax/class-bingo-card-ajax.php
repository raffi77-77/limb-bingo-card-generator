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
//        add_action('wp_ajax_nopriv_{$action}', array($this, '{action}'));
//        add_action('wp_ajax_{$action}', array($this, '{action}'));
    }
}