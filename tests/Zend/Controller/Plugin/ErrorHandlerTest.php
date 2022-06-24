<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Controller_Plugin_ErrorHandlerTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD"))
{
    define("PHPUnit_MAIN_METHOD", "Zend_Controller_Plugin_ErrorHandlerTest::main");
    $basePath = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..');
    set_include_path(
        $basePath . DIRECTORY_SEPARATOR . 'tests'
        . PATH_SEPARATOR . $basePath . DIRECTORY_SEPARATOR . 'library'
        . PATH_SEPARATOR . get_include_path()
    );
}


require_once 'Zend/Controller/Plugin/ErrorHandler.php';
require_once 'Zend/Controller/Request/Http.php';
require_once 'Zend/Controller/Response/Http.php';

require_once 'Zend/Controller/Dispatcher/Exception.php';
require_once 'Zend/Controller/Action/Exception.php';

require_once 'Zend/Controller/Front.php';

/**
 * Test class for Zend_Controller_Plugin_ErrorHandler.
 * Generated by PHPUnit_Util_Skeleton on 2007-05-15 at 09:50:21.
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Plugin
 */
class Zend_Controller_Plugin_ErrorHandlerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Request object
     * @var Zend_Controller_Request_Http
     */
    public $request;

    /**
     * Response object
     * @var Zend_Controller_Response_Http
     */
    public $response;

    /**
     * Error handler plugin
     * @var Zend_Controller_Plugin_ErrorHandler
     */
    public $plugin;

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {

        $suite  = new \PHPUnit\Framework\TestSuite("Zend_Controller_Plugin_ErrorHandlerTest");
        $result = \PHPUnit\TextUI\TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp(): void
    {
        Zend_Controller_Front::getInstance()->resetInstance();
        $this->request  = new Zend_Controller_Request_Http();
        $this->response = new Zend_Controller_Response_Http();
        $this->plugin   = new Zend_Controller_Plugin_ErrorHandler();

        $this->plugin->setRequest($this->request);
        $this->plugin->setResponse($this->response);
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown(): void
    {
    }

    public function testSetErrorHandler()
    {
        $this->plugin->setErrorHandler([
            'module'     => 'myfoo',
            'controller' => 'bar',
            'action'     => 'boobaz',
        ]);

        $this->assertEquals('myfoo', $this->plugin->getErrorHandlerModule());
        $this->assertEquals('bar', $this->plugin->getErrorHandlerController());
        $this->assertEquals('boobaz', $this->plugin->getErrorHandlerAction());
    }

    public function testSetErrorHandlerModule()
    {
        $this->plugin->setErrorHandlerModule('boobah');
        $this->assertEquals('boobah', $this->plugin->getErrorHandlerModule());
    }

    public function testSetErrorHandlerController()
    {
        $this->plugin->setErrorHandlerController('boobah');
        $this->assertEquals('boobah', $this->plugin->getErrorHandlerController());
    }

    public function testSetErrorHandlerAction()
    {
        $this->plugin->setErrorHandlerAction('boobah');
        $this->assertEquals('boobah', $this->plugin->getErrorHandlerAction());
    }

    public function testPostDispatchNoControllerException()
    {
        $this->response->setException(new Zend_Controller_Dispatcher_Exception('Testing controller exception'));
        $this->request->setModuleName('foo')
                      ->setControllerName('bar')
                      ->setActionName('baz');
        $this->plugin->postDispatch($this->request);

        $this->assertNotNull($this->request->getParam('error_handler'));
        $errorHandler = $this->request->getParam('error_handler');
        $this->assertTrue($errorHandler instanceof ArrayObject);
        $this->assertEquals(Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER, $errorHandler->type);

        $this->assertEquals('error', $this->request->getActionName());
        $this->assertEquals('error', $this->request->getControllerName());
        $this->assertEquals('default', $this->request->getModuleName());
    }

    public function testPostDispatchNoActionException()
    {
        $this->response->setException(new Zend_Controller_Action_Exception('Testing action exception', 404));
        $this->request->setModuleName('foo')
                      ->setControllerName('bar')
                      ->setActionName('baz');
        $this->plugin->postDispatch($this->request);

        $this->assertNotNull($this->request->getParam('error_handler'));
        $errorHandler = $this->request->getParam('error_handler');
        $this->assertTrue($errorHandler instanceof ArrayObject);
        $this->assertEquals(Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION, $errorHandler->type);

        $this->assertEquals('error', $this->request->getActionName());
        $this->assertEquals('error', $this->request->getControllerName());
        $this->assertEquals('default', $this->request->getModuleName());
    }

    public function testPostDispatchOtherException()
    {
        $this->response->setException(new Exception('Testing other exception'));
        $this->request->setModuleName('foo')
                      ->setControllerName('bar')
                      ->setActionName('baz');
        $this->plugin->postDispatch($this->request);

        $this->assertNotNull($this->request->getParam('error_handler'));
        $errorHandler = $this->request->getParam('error_handler');
        $this->assertTrue($errorHandler instanceof ArrayObject);
        $this->assertEquals(Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER, $errorHandler->type);

        $this->assertEquals('error', $this->request->getActionName());
        $this->assertEquals('error', $this->request->getControllerName());
        $this->assertEquals('default', $this->request->getModuleName());
    }

    public function testPostDispatchThrowsWhenCalledRepeatedly()
    {
        $this->response->setException(new Exception('Testing other exception'));
        $this->request->setModuleName('foo')
                      ->setControllerName('bar')
                      ->setActionName('baz');
        $this->plugin->postDispatch($this->request);

        $this->response->setException(new Zend_Controller_Dispatcher_Exception('Another exception'));
        try {
            $this->plugin->postDispatch($this->request);
            $this->fail('Repeated calls with new exceptions should throw exceptions');
        } catch (Exception $e) {
            $type = get_class($e);
            $this->assertEquals('Zend_Controller_Dispatcher_Exception', $type);
            $this->assertEquals('Another exception', $e->getMessage());
        }
    }

    public function testPostDispatchDoesNothingWhenCalledRepeatedlyWithoutNewExceptions()
    {
        $this->response->setException(new Exception('Testing other exception'));
        $this->request->setModuleName('foo')
                      ->setControllerName('bar')
                      ->setActionName('baz');
        $this->plugin->postDispatch($this->request);

        try {
            $this->plugin->postDispatch($this->request);
        } catch (Exception $e) {
            $this->fail('Repeated calls with no new exceptions should not throw exceptions');
        }
    }

    public function testPostDispatchWithoutException()
    {
        $this->request->setModuleName('foo')
                      ->setControllerName('bar')
                      ->setActionName('baz');
        $this->plugin->postDispatch($this->request);
        $this->assertEquals('baz', $this->request->getActionName());
        $this->assertEquals('bar', $this->request->getControllerName());
        $this->assertEquals('foo', $this->request->getModuleName());
    }

    public function testPostDispatchErrorRequestIsClone()
    {
        $this->response->setException(new Zend_Controller_Dispatcher_Exception('Testing controller exception'));
        $this->request->setModuleName('foo')
                      ->setControllerName('bar')
                      ->setActionName('baz');
        $this->plugin->postDispatch($this->request);

        $this->assertNotNull($this->request->getParam('error_handler'));
        $errorHandler = $this->request->getParam('error_handler');
        $this->assertTrue($errorHandler instanceof ArrayObject);
        $this->assertTrue($errorHandler->request instanceof Zend_Controller_Request_Http);
        $this->assertNotSame($this->request, $errorHandler->request);
    }

    public function testPostDispatchQuitsWithFalseUserErrorHandlerParam()
    {
        $front = Zend_Controller_Front::getInstance();
        $front->resetInstance();
        $front->setParam('noErrorHandler', true);

        $this->response->setException(new Zend_Controller_Dispatcher_Exception('Testing controller exception'));
        $this->request->setModuleName('foo')
                      ->setControllerName('bar')
                      ->setActionName('baz');
        $this->plugin->postDispatch($this->request);

        $this->assertNull($this->request->getParam('error_handler'));
    }
}

// Call Zend_Controller_Plugin_ErrorHandlerTest::main() if this source file is executed directly.
if (\PHPUnit\MAIN\METHOD == "Zend_Controller_Plugin_ErrorHandlerTest::main")
{
    Zend_Controller_Plugin_ErrorHandlerTest::main();
}

