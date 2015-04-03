<?php
/**
 * Slim Framework (http://slimframework.com)
 *
 * @link      https://github.com/endel/slim-lightncandy-view
 * @copyright Copyright (c) 2015 Endel Dreyer
 * @license   https://github.com/endel/slim-lightncandy-view/blob/master/LICENSE (MIT License)
 */
namespace Slim\Tests\Views;

require dirname(__DIR__) . '/vendor/autoload.php';

class LightncandyTest extends \PHPUnit_Framework_TestCase
{
    protected $view;

    public function setUp()
    {
        $this->view = new \Slim\Views\Lightncandy(dirname(__FILE__) . '/templates');
    }

    public function testFetch()
    {
        $output = $this->view->fetch('example', [
            'name' => 'Endel'
        ]);

        $this->assertEquals("<p>Hi, my name is Endel.</p>\n", $output);
    }

    public function testRender()
    {
        $this->expectOutputString("<p>Hi, my name is Endel.</p>\n");
        $this->view->render('example', [
            'name' => 'Endel'
        ]);
    }
}
