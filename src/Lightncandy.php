<?php
/**
 * Slim Framework (http://slimframework.com)
 *
 * @link      https://github.com/endel/slim-lightncandy-view
 * @copyright Copyright (c) 2015 Endel Dreyer
 * @license   https://github.com/endel/slim-lightncandy-view/blob/master/LICENSE.md (MIT License)
 */
namespace Slim\Views;

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
    public $context;
    public $yield_blocks;

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
        $this->context = new SplStack();
        $this->yield_blocks = array();
        $this->helpers = new \Slim\Helper\Set($this->getHelpers());
        $this->block_helpers = new \Slim\Helper\Set($this->getBlockHelpers());
    }

    /**
     * Register service with container
     *
     * @param Container $container The Pimple container
     */
    public function register(\Pimple\Container $container)
    {
        // // Register urlFor Twig function
        // $this->environment->addFunction(new \Twig_SimpleFunction('url_for', function ($name, $data = []) use ($container) {
        //     return $container['router']->urlFor($name, $data);
        // }));

        // Register this view with the Slim container
        $container['view'] = $this;
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
        $php = LightnCandy::compile($this->getTemplate($name), array(
            'flags' => LightnCandy::FLAG_ERROR_EXCEPTION | LightnCandy::FLAG_ERROR_LOG |
            LightnCandy::FLAG_INSTANCE |
            LightnCandy::FLAG_MUSTACHE |
            LightnCandy::FLAG_HANDLEBARS,
            'basedir' => $this->directories,
            'fileext' => $this->extensions,
            'helpers' => $this->helpers->all(),
            'hbhelpers' => $this->block_helpers->all()
        ));

        $renderer = LightnCandy::prepare($php);
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
            'yield' => 'Hook\\View\\Helper::yieldContent',

            // string helpers
            'str_plural' => 'Hook\\View\\Helper::str_plural',
            'str_singular' => 'Hook\\View\\Helper::str_singular',
            'uppercase' => 'Hook\\View\\Helper::uppercase',
            'lowercase' => 'Hook\\View\\Helper::lowercase',
            'camel_case' => 'Hook\\View\\Helper::camel_case',
            'snake_case' => 'Hook\\View\\Helper::snake_case',

            // url helpers
            'link_to' => 'Hook\\View\\Helper::link_to',
            'stylesheet' => 'Hook\\View\\Helper::stylesheet',
            'javascript' => 'Hook\\View\\Helper::javascript',

            // form helpers
            'input' => 'Hook\\View\\Helper::input',
            'select' => 'Hook\\View\\Helper::select',

            // data helpers
            'count' => 'Hook\\View\\Helper::count',
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
            'content_for' => 'Hook\\View\\BlockHelper::content_for',

            // url helpers
            'link_to' => 'Hook\\View\\BlockHelper::link_to',

            // form helpers
            'form' => 'Hook\\View\\BlockHelper::form',
            'form_for' => 'Hook\\View\\BlockHelper::form_for'
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
        throw new NotFoundException("Template not found.");
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
