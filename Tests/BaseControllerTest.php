<?php
namespace SlaxWeb\BaseController;

require_once("Support/TestSupport.php");

use \Mockery as m;

$helperOutput = false;
$existing404 = false;

function show_404()
{
    global $helperOutput;

    if ($helperOutput) {
        echo "show_404";
    }
}

function method_exists($class, $method)
{
    global $helperOutput;
    global $existing404;

    if ($helperOutput) {
        echo "{$method}\n";
    }

    if ($method === "_404") {
        return $existing404;
    }

    return \method_exists($class, $method);
}

class BaseControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testMissingMethod()
    {
        global $helperOutput;
        $helperOutput = true;

        $c = $this->getMockBuilder("\\SlaxWeb\\BaseController\\BaseController")
            ->setMethods(["_loadLanguage", "_loadModels"])
            ->getMock();

        $this->expectOutputRegex("~show_404~");
        $c->_remap("missingMethod");
    }

    public function testPostMethodRename()
    {
        global $helperOutput;
        $helperOutput = true;

        $c = $this->getMockBuilder("\\SlaxWeb\\BaseController\\BaseController")
            ->setMethods(["_loadLanguage", "_loadViews", "_callback", "_loadModels"])
            ->getMock();

        $this->expectOutputRegex("~missingMethod_post~");
        $c->input->server["REQUEST_METHOD"] = "POST";
        $c->_remap("missingMethod");
    }

    public function testCustom404()
    {
        global $helperOutput;
        global $existing404;
        $helperOutput = true;
        $existing404 = true;

        $c = $this->getMockBuilder("\\SlaxWeb\\BaseController\\BaseController")
            ->setMethods(["_loadLanguage", "_loadViews", "_callback", "_loadModels"])
            ->getMock();

        $this->expectOutputRegex("~custom_404~");

        $c->expects($this->once())
            ->method("_loadViews")
            ->willReturn(true);

        $c->_remap("custom404");
    }

    public function testExistingMethodRemap()
    {
        $c = $this->getMockBuilder("\\SlaxWeb\\BaseController\\BaseController")
            ->setMethods(["_loadLanguage", "_loadViews", "_callback", "_loadModels"])
            ->getMock();

        $this->expectOutputRegex("~testMethod~");

        $c->expects($this->once())
            ->method("_loadViews")
            ->willReturn(true);

        $c->expects($this->exactly(2))
            ->method("_callback")
            ->willReturn(true);

        $c->_remap("testMethod");
    }
}
