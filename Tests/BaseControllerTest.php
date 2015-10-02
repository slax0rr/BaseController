<?php
namespace SlaxWeb\BaseController;

require_once("Support/TestSupport.php");

use \Mockery as m;

function show_404()
{
    echo "missingMethod";
}

class BaseControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testMissingMethod()
    {
        $c = $this->getMockBuilder("\\SlaxWeb\\BaseController\\BaseController")
            ->setMethods(["_loadLanguage", "_loadViews", "_callback", "_loadModels"])
            ->getMock();

        $this->expectOutputString("missingMethod");
        $c->_remap("missingMethod");
    }
}
