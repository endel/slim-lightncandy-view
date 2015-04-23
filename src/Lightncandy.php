<?php
/**
 * Slim Framework (http://slimframework.com)
 *
 * @link      https://github.com/endel/slim-lightncandy-view
 * @copyright Copyright (c) 2015 Endel Dreyer
 * @license   https://github.com/endel/slim-lightncandy-view/blob/master/LICENSE.md (MIT License)
 */
namespace Slim\Views;

use LightnCandy as Engine;
use LCRun3;

use Exception;

/**
 * Lightncandy View
 *
 * This class is a Slim Framework view helper built
 * on top of the Lightncandy templating component. Lightncandy is
 * a PHP component created by @zordius.
 *
 * @link https://github.com/zordius/lightncandy
 */
class Lightncandy implements \Pimple\ServiceProviderInterface
{
    /**
     * container
     *
     * @var \Pimple\Container
     */
    public static $container;

    /**
     * helpers
     *
     * @var \Slim\Helper\Set
     */
    public $helpers;

    /**
     * block_helpers
     *
     * @var \Slim\Helper\Set
     */
    public $block_helpers;

    /**
     * context
     *
     * @var SplStack
     */
    public static $context;
    public static $yield_blocks;

    /**
     * template_string
     *
     * @var string
     */
    protected $template_string;

    protected $extensions = array('.hbs', '.handlebars', '.mustache', '.html');
    protected $directories = array();

    /**
     * Default view variables
     *
     * @var array
     */
    protected $defaultVariables = [];

    /********************************************************************************
     * Constructors and service provider registration
     *******************************************************************************/

    /**
     * Create new Lightncandy view
     *
     * @param string $path     Path to templates directory
     * @param array  $settings Twig environment settings
     */
    public function __construct($path, $settings = [])
    {
        static::$context = new \SplStack();
        static::$yield_blocks = array();

        array_push($this->directories, $path);

        $this->helpers = $this->getHelpers();
        if (isset($settings['helpers'])) {
            $this->helpers = array_merge($this->helpers, $settings['helpers']);
        }

        $this->block_helpers = $this->getBlockHelpers();
        if (isset($settings['helpers'])) {
            $this->block_helpers = array_merge($this->block_helpers, $settings['block_helpers']);
        }
    }

    /**
     * Register service with container
     *
     * @param Container $container The Pimple container
     */
    public function register(\Pimple\Container $container)
    {
        // Register this view with the Slim container
        $container['view'] = $this;

        // TODO: the container should be tied to a single Lightncandy instance
        static::$container = $container;
    }

    /********************************************************************************
     * Methods
     *******************************************************************************/

    /**
     * Fetch rendered template
     *
     * @param  string $template Template pathname relative to templates directory
     * @param  array  $data     Associative array of template variables
     *
     * @return string
     */
    public function fetch($template, $data = [])
    {
        $data = array_merge($this->defaultVariables, $data);
        $php = Engine::compile($this->getTemplate($template), array(
            'flags' => Engine::FLAG_ERROR_EXCEPTION | Engine::FLAG_ERROR_LOG |
            Engine::FLAG_INSTANCE |
            Engine::FLAG_MUSTACHE |
            Engine::FLAG_HANDLEBARS,
            'basedir' => $this->directories,
            'fileext' => $this->extensions,
            'helpers' => $this->helpers,
            'hbhelpers' => $this->block_helpers
        ));

        $renderer = Engine::prepare($php);
        return $renderer(array_merge($data ?: array()), LCRun3::DEBUG_ERROR_LOG);
    }

    /**
     * Output rendered template
     *
     * @param  string $template Template pathname relative to templates directory
     * @param  array $data      Associative array of template variables
     */
    public function render($template, $data = [])
    {
        echo $this->fetch($template, $data);
    }


    /********************************************************************************
     * Protected Methods
     *******************************************************************************/

    protected function getHelpers() {
        $helpers = array(
            // core helpers
            'yield' => 'Slim\\Views\\Helpers\\Helper::yieldContent',

            // string helpers
            'uppercase' => 'Slim\\Views\\Helpers\\Helper::uppercase',
            'lowercase' => 'Slim\\Views\\Helpers\\Helper::lowercase',

            // url helpers
            'link_to' => 'Slim\\Views\\Helpers\\Helper::link_to',
            'stylesheet' => 'Slim\\Views\\Helpers\\Helper::stylesheet',
            'javascript' => 'Slim\\Views\\Helpers\\Helper::javascript',

            // form helpers
            'input' => 'Slim\\Views\\Helpers\\Helper::input',
            'textarea' => 'Slim\\Views\\Helpers\\Helper::textarea',
            'select' => 'Slim\\Views\\Helpers\\Helper::select',

            // data helpers
            'count' => 'Slim\\Views\\Helpers\\Helper::count',
        );

        // $helper_files = glob(Router::config('templates.helpers_path') . '/*');
        // foreach($helper_files as $helper) {
        //     $helpers = array_merge($helpers, require($helper));
        // }

        return $helpers;
    }

    protected function getBlockHelpers() {
        return array(
            // core helpers
            'content_for' => 'Slim\\Views\\Helpers\\BlockHelper::content_for',

            // url helpers
            'link_to' => 'Slim\\Views\\Helpers\\BlockHelper::link_to',

            // form helpers
            'form' => 'Slim\\Views\\Helpers\\BlockHelper::form',
            'form_for' => 'Slim\\Views\\Helpers\\BlockHelper::form_for'
        );
    }

    protected function getTemplate($name) {
        foreach ($this->directories as $dir) {
            foreach ($this->extensions as $ext) {
                $path = $dir . DIRECTORY_SEPARATOR . ltrim($name . $ext, DIRECTORY_SEPARATOR);
                if (file_exists($path)) {
                    return file_get_contents($path);
                }
            }
        }
        throw new Exception("Template not found.");
    }

    /********************************************************************************
     * ArrayAccess interface
     *******************************************************************************/

    /**
     * Does this collection have a given key?
     *
     * @param  string $key The data key
     *
     * @return bool
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->defaultVariables);
    }

    /**
     * Get collection item for key
     *
     * @param string $key The data key
     *
     * @return mixed The key's value, or the default value
     */
    public function offsetGet($key)
    {
        return $this->defaultVariables[$key];
    }

    /**
     * Set collection item
     *
     * @param string $key   The data key
     * @param mixed  $value The data value
     */
    public function offsetSet($key, $value)
    {
        $this->defaultVariables[$key] = $value;
    }

    /**
     * Remove item from collection
     *
     * @param string $key The data key
     */
    public function offsetUnset($key)
    {
        unset($this->defaultVariables[$key]);
    }

    /********************************************************************************
     * Countable interface
     *******************************************************************************/

    /**
     * Get number of items in collection
     *
     * @return int
     */
    public function count()
    {
        return count($this->defaultVariables);
    }

    /********************************************************************************
     * IteratorAggregate interface
     *******************************************************************************/

    /**
     * Get collection iterator
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->defaultVariables);
    }
}
