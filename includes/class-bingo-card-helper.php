<?php

/**
 * The class the helps with different static methods
 */
class BingoCardHelper
{
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
     * @return string
     */
    public static function get_bg_default_content($type, $size) {
        if (empty($type) || empty($size)) {
            $type = '1-9';
            $size = '3x3';
        }
        switch ($type) {
            case '1-9':
                return implode("\n", range(1, 9));
            case '1-25':
                return implode("\n", range(1, 25));
            case '1-80':
                if ($size === '3x3') {
                    $to = 36;
                } elseif ($size === '4x4') {
                    $to = 64;
                } else {
                    $to = 80;
                }
                return implode("\n", range(1, $to));
            case '1-100':
                if ($size === '3x3') {
                    $to = 36;
                } elseif ($size === '4x4') {
                    $to = 64;
                } else {
                    $to = 100;
                }
                return implode("\n", range(1, $to));
        }
        return '';
    }
}