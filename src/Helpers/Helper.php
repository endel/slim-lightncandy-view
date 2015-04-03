<?php namespace Slim\Views\Helpers;

use Hook\Http\Request;

class Helper {

    //
    // Core helpers
    //

    public static function yieldContent($args) {
        $content = isset($args[0]) ? $args[0] : '__yield__';
        $yield_blocks = Slim\Views\Lightncandy::$yield_blocks;
        return array(isset($yield_blocks[$content]) ? $yield_blocks[$content] : "", 'raw');
    }

    //
    // String helpers
    //

    public static function lowercase($args) {
        return strtolower($string[0]);
    }

    public static function uppercase($args) {
        return strtoupper($string[0]);
    }

    //
    // URL helpers
    //

    public static function link_to($args, $attributes) {
        $text = (isset($args[1])) ? $args[1] : $args[0];
        return array('<a href="/'.$args[0].'"' . \Slim\Views\Helpers\Helper::html_attributes($attributes) . '>' . $text . '</a>', 'raw');
    }

    public static function stylesheet($args, $attributes) {
        $url = preg_replace('/index\.php\//', '', str_finish(Request::getRootUri(), '/')) . $args[0];
        $media = (isset($attributes['media'])) ? $attributes['media'] : 'screen';
        return array('<link href="' . $url . '" media="' . $media . '" rel="stylesheet" />', 'raw');
    }

    public static function javascript($args, $attributes) {
        $url = preg_replace('/index\.php\//', '', str_finish(Request::getRootUri(), '/')) . $args[0]; // Request::getRootUri()
        return array('<script src="' . $url . '"></script>', 'raw');
    }

    //
    // Form helpers
    //

    public static function input($args, $attributes) {
        if (!isset($attributes['name']) && isset($args[0])) {
            // TODO: analyse context recursively
            if (Slim\Views\Lightncandy::$context->count() > 0) {
                $attributes['name'] = Slim\Views\Lightncandy::$context->top() . '['.$args[0].']';
            } else {
                $attributes['name'] = $args[0];
            }
        }

        if (isset($attributes['options'])) {
            return \Slim\Views\Helpers\Helper::select($args, $attributes);
        }

        // use 'text' as default input type
        if (!isset($attributes['type'])) {
            $is_type_as_name = in_array($attributes['name'], array('email', 'password', 'date'));
            $attributes['type'] = $is_type_as_name ? $attributes['name'] : 'text';
        }

        return array('<input' . \Slim\Views\Helpers\Helper::html_attributes($attributes) . ' />', 'raw');
    }

    public static function textarea($args, $attributes) {
        if (!isset($attributes['name']) && isset($args[0])) {
            // TODO: analyse context recursively
            if (Slim\Views\Lightncandy::$context->count() > 0) {
                $attributes['name'] = Slim\Views\Lightncandy::$context->top() . '['.$args[0].']';
            } else {
                $attributes['name'] = $args[0];
            }
        }

        return array('<textarea' . \Slim\Views\Helpers\Helper::html_attributes($attributes) . '></textarea>', 'raw');
    }

    public static function select($args, $attributes) {
        $options = array_remove($attributes, 'options');
        $selected_option = array_remove($attributes, 'selected');

        if (!isset($attributes['name']) && isset($args[0])) {
            // TODO: analyse context recursively
            if (Slim\Views\Lightncandy::$context->count() > 0) {
                $attributes['name'] = Slim\Views\Lightncandy::$context->top() . '['.$args[0].']';
            } else {
                $attributes['name'] = $args[0];
            }
        }

        $html_options = '';
        foreach($options as $key => $value) {
            $key = isset($value['_id']) ? $value['_id'] : $key;
            $value = isset($value['name']) ? $value['name'] : $value;
            $is_selected = ($selected_option == $key) ? ' selected="selected"' : '';
            $html_options .= '<option value="' . $key . '"' . $is_selected . '>' . $value . '</option>';
        }

        return array('<select' . static::html_attributes($attributes) . '>'.$html_options.'</select>', 'raw');
    }

    //
    // Integer helpers
    //

    public static function count($args) {
        return count($args[0]);
    }

    //
    // Miscelaneous helpers
    //

    public static function paginate($args, $named) {
        $collection = $args[0];

        if (!method_exists($collection, 'links')) {
            return "paginate: must have 'links' method.";
        }

        // pagination window
        if (isset($named['window'])) {
            $collection->getEnvironment()->setPaginationWindow($named['window']);
        }

        return array($args[0]->links(), 'raw');
    }

    public static function html_attributes($attributes) {
        $tag_attributes = "";
        foreach ($attributes as $key => $value) {
            $tag_attributes .= ' ' . $key . '="' . $value . '"';
        }
        return $tag_attributes;
    }

}

