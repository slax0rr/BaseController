<?php
namespace SlaxWeb\BaseController;

require_once("Support/TestSupport.php");
require_once("Support/GlobalSupport.php");

use \Mockery as m;

class BaseControllerTest extends \PHPUnit_Framework_TestCase
{
    /*
     * Test Remap Loads Language
     *
     * Remap method must load the defined language
     */
    public function testRemapLanguage()
    {
        $c = $this->getMockBuilder("\\SlaxWeb\\BaseController\\BaseController")
            ->setMethods(array("_loadLanguage", "_loadViews", "_callback", "_loadModels"))
            ->getMock();

        $this->expectOutputRegex("~testMethod~");

        $c->expects($this->once())
            ->method("_loadLanguage")
            ->willReturn(true);

        $c->_remap("testMethod");
    }

    /*
     * Test Missing Method
     *
     * When a method it missing, and custom 404 error method is not defined
     * BaseController must call the CodeIgniter show_404 global function.
     */
    public function testMissingMethod()
    {
        global $helperOutput;
        $helperOutput = true;

        $c = $this->getMockBuilder("\\SlaxWeb\\BaseController\\BaseController")
            ->setMethods(array("_loadLanguage", "_loadModels"))
            ->getMock();

        $this->expectOutputRegex("~show_404~");
        $c->_remap("missingMethod");
    }

    /*
     * Test Custom 404
     *
     * When a method is missing, and a custom 404 error method is defined,
     * BaseController must call it, and load the views as if a normal
     * controller method was called
     */
    public function testCustom404()
    {
        global $helperOutput;
        global $existing404;
        $helperOutput = true;
        $existing404 = true;

        $c = $this->getMockBuilder("\\SlaxWeb\\BaseController\\BaseController")
            ->setMethods(array("_loadLanguage", "_loadViews", "_callback", "_loadModels"))
            ->getMock();

        $this->expectOutputRegex("~custom_404~");

        $c->expects($this->once())
            ->method("_loadViews")
            ->willReturn(true);

        $c->_remap("custom404");
    }

    /*
     * Test POST Method Rename
     *
     * When a request method POST is received, BaseController must append
     * "_post" to the controller method.
     */
    public function testPostMethodRename()
    {
        global $helperOutput;
        $helperOutput = true;

        $c = $this->getMockBuilder("\\SlaxWeb\\BaseController\\BaseController")
            ->setMethods(array("_loadLanguage", "_loadViews", "_callback", "_loadModels"))
            ->getMock();

        $this->expectOutputRegex("~missingMethod_post~");
        $c->input->server["REQUEST_METHOD"] = "POST";
        $c->_remap("missingMethod");
    }

    /*
     * Test Existing Method Remap
     *
     * When everything is alright with the request, and the method exists,
     * it should be called, along with two callbacks and at the end the views
     * must be loaded.
     */
    public function testExistingMethodRemap()
    {
        $c = $this->getMockBuilder("\\SlaxWeb\\BaseController\\BaseController")
            ->setMethods(array("_loadLanguage", "_loadViews", "_callback", "_loadModels"))
            ->getMock();

        $c->beforeMethod = array("beforeMethod");
        $c->afterMethod = array("afterMethod");

        $this->expectOutputRegex("~testMethod~");

        $c->expects($this->once())
            ->method("_loadViews")
            ->willReturn(true);

        $c->expects($this->exactly(2))
            ->method("_callback")
            ->withConsecutive(
                array($this->equalTo($c->beforeMethod)),
                array($this->equalTo($c->afterMethod))
            )
            ->willReturn(true);
        $c->_remap("testMethod");
    }

    /*
     * Test CRUD - GET
     *
     * The GET part of CRUD should only obtain data from the database
     * and load it into the view data.
     */
    public function testCrudGet()
    {
        $c = $this->getMockBuilder("\\SlaxWeb\\BaseController\\BaseController")
            ->setMethods(array("_loadLanguage", "_loadViews", "_callback", "_loadModels"))
            ->getMock();

        $c->TestController = $this->getMockBuilder("model")
            ->setMethods(array("get"))
            ->getMock();

        $c->TestController->expects($this->once())
            ->method("get")
            ->with($this->equalTo(123))
            ->willReturn(array("model" => "data"));

        $c->viewData = array("existing" => "data");

        $c->index(123);

        $this->assertEquals(
            array("existing" => "data", "_tableData" => array("model" => "data")),
            $c->viewData
        );
    }
}
