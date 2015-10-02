<?php
namespace SlaxWeb\BaseController;

require_once("Support/TestSupport.php");

use \Mockery as m;

function show_404()
{
    echo "show_404";
}

function method_exists($class, $method)
{
    echo "{$method}\n";

    return \method_exists($class, $method);
}

class BaseControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testMissingMethod()
    {
        $c = $this->getMockBuilder("\\SlaxWeb\\BaseController\\BaseController")
            ->setMethods(["_loadLanguage", "_loadModels"])
            ->getMock();

        $this->expectOutputRegex("~show_404~");
        $c->_remap("missingMethod");
    }

    public function testPostMethodRename()
    {
        $c = $this->getMockBuilder("\\SlaxWeb\\BaseController\\BaseController")
            ->setMethods(["_loadLanguage", "_loadViews", "_callback", "_loadModels"])
            ->getMock();

        $this->expectOutputRegex("~missingMethod_post~");
        $c->input->server["REQUEST_METHOD"] = "POST";
        $c->_remap("missingMethod");
    }
}
