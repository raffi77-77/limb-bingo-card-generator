<?php

class BingoCardAdmin
{
    public function register_dependencies() {
        add_action('add_meta_boxes', array($this, 'add_custom_meta_boxes'));
    }

    public function add_custom_meta_boxes() {

    }
}