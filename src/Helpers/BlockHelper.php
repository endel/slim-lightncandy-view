<?php namespace Slim\Views\Helpers;

use Slim\Views\Helpers\Helper;

class BlockHelper {

    //
    // Core helpers
    //

    public static function content_for($context, $options) {
        Router::getInstance()->view->yield_blocks[$context] = $options['fn']();
        return false;
    }

    //
    // URL helpers
    //
    public static function link_to() {
        $args = func_get_args();
        $options = array_pop($args);

        if (isset($options['fn'])) {
            array_push($args, PHP_EOL.$options['fn']());
        }

        return Slim\Views\Helpers\Helper::link_to($args, $options['hash']);
    }

    //
    // Form helpers
    //

    public static function form() {
        $args = func_get_args();
        $options = array_pop($args);

        // use GET method as default
        if (!isset($options['hash']['method'])) {
            $options['hash']['method'] = 'get';
        }

        $html = '<form' . Slim\Views\Helpers\Helper::html_attributes($options['hash']) . '>' . "\n" .
            $options['fn']() .
        '</form>';

        return $html;
    }

    public static function form_for($context, $options) {
        Router::getInstance()->view->context->push($context);
        $html = \Hook\Framework\BlockHelper::form($context, $options);
        Router::getInstance()->view->context->pop();
        return $html;
    }

}


