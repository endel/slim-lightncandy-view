<?php
/**
 * Slim Framework (http://slimframework.com)
 *
 * @link      https://github.com/endel/slim-lightncandy-view
 * @copyright Copyright (c) 2015 Endel Dreyer
 * @license   https://github.com/endel/slim-lightncandy-view/blob/master/LICENSE (MIT License)
 */
namespace Slim\Tests\Views;

use Slim\Views\Lightncandy;

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

        $mockBody = $this->getMockBuilder('Psr\Http\Message\StreamInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $mockResponse = $this->getMockBuilder('Psr\Http\Message\ResponseInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $mockBody->expects($this->once())
            ->method('write')
            ->with("<p>Hi, my name is Endel.</p>\n")
            ->willReturn(28);

        $mockResponse->expects($this->once())
            ->method('getBody')
            ->willReturn($mockBody);

        $response = $this->view->render( $mockResponse, 'example', [
            'name' => 'Endel'
        ]);

        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $response);
    }
}
