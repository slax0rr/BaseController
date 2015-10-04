<?php
namespace SlaxWeb\BaseController;

require_once("Support/TestSupport.php");
require_once("Support/GlobalSupport.php");
require_once("Support/ControllerOverride.php");

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
     * Test CRUD - RETRIEVE
     *
     * The RETRIEVE part of CRUD should only obtain data from the database
     * and load it into the view data.
     */
    public function testCrudRetrieve()
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

    /*
     * Test CRUD - CREATE
     *
     * The CREATE part of CRUD must retrieve data from POST, set the CREATE
     * validation rules, store the data, and handle errors.
     */
    public function testCrudCreate()
    {
        $this->_testCrud("create");
    }

    /*
     * Test CRUD - UPDATE
     *
     * The UPDATE part of CRUD must retrieve data from POST, assign rules,
     * update the database, set the "afterUpdate" view, and check for errors.
     */
    public function testCrudUpdate()
    {
        $this->_testCrud("update");
    }

    /*
     * Test CRUD - DELETE
     *
     * The DELETE part of crud expects an ID at input, and calls the delete
     * on the database. After it sets the "afterDelete" view, and checks
     * for errors.
     */
    public function testCrudDelete()
    {
        $c = $this->getMockBuilder("\\SlaxWeb\\BaseController\\BaseController")
            ->setMethods(array("_loadLanguage", "_loadViews", "_callback", "_loadModels"))
            ->getMock();

        $c->afterDelete = "afterDeleteView";

        // Error object
        $error = new \stdClass;

        // Status mock object
        $status = $this->getMockBuilder("status")
            ->setMethods(array("error"))
            ->getMock();

        // Error mock method
        $status->expects($this->once())
            ->method("error")
            ->willReturn($error);

        // Model mock object
        $c->TestController = $this->getMockBuilder("model")
            ->setMethods(array("delete"))
            ->getMock();

        // Delete mock method
        $c->TestController->expects($this->exactly(3))
            ->method("delete")
            ->with(123)
            ->will($this->onConsecutiveCalls(true, false, $status));

        // Mock language helper
        $c->lang = $this->getMockBuilder("languageHelper")
            ->setMethods(array("line"))
            ->getMock();

        $c->lang->expects($this->once())
            ->method("line")
            ->with("error_delete_generic")
            ->willReturn("Generic Delete Error Message");

        // Test - everything ok
        $c->delete_post(123);
        $this->assertEquals($c->afterDelete, $c->view);
        $this->assertEquals($c->viewData, array());

        // Test - unknown error
        $c->delete_post(123);
        $this->assertEquals($c->afterDelete, $c->view);
        $this->assertEquals(array("deleteError" => "Generic Delete Error Message"), $c->viewData);

        // Test - delete error
        $error->message = "Delete Error Message";
        $c->delete_post(123);
        $this->assertEquals($c->afterDelete, $c->view);
        $this->assertEquals(array("deleteError" => "Delete Error Message"), $c->viewData);
    }

    /*
     * Test Model Autoload
     *
     * The model autoloader has to call "beforeModel", and "afterModel"
     * callbacks at the beginning and the end of method execution.
     * It checks if "models" property has not been set to false, or not set
     * at all, if it is indeed set, it first tries to load the model that
     * has teh same name as the controller, and if "models" property
     * is an array, it itterates through it, and loads all defined models
     * in that array.
     */
    public function testModelAutoload()
    {
        $c = $this->getMockBuilder("ControllerOverride")
            ->setMethods(array("_callback"))
            ->getMock();

        define("APPPATH", "mockPath/");
        global $helperOutput;
        global $fileExists;
        $c->beforeModel = array("beforeModel");
        $c->afterModel = array("afterModel");
        $c->models = array("CustomModel1", "CustomModel2");

        $c->expects($this->exactly(6))
            ->method("_callback")
            ->withConsecutive(
                array($this->equalTo($c->beforeModel)),
                array($this->equalTo($c->afterModel)),
                array($this->equalTo($c->beforeModel)),
                array($this->equalTo($c->afterModel)),
                array($this->equalTo($c->beforeModel)),
                array($this->equalTo($c->afterModel))
            );

        $c->load = $this->getMockBuilder("loaderOverride")
            ->setMethods(array("model"))
            ->getMock();

        $c->load->expects($this->exactly(5))
            ->method("model")
            ->withConsecutive(
                array($this->equalTo("TestController_model"), $this->equalTo("TestController")),
                array($this->equalTo("CustomModel1_model"), $this->equalTo("CustomModel1")),
                array($this->equalTo("CustomModel2_model"), $this->equalTo("CustomModel2")),
                array($this->equalTo("CustomModel1_model"), $this->equalTo("CustomModel1")),
                array($this->equalTo("CustomModel2_model"), $this->equalTo("CustomModel2"))
            );

        $this->expectOutputRegex("~mockPath/models/TestController_model.php$~");

        $helperOutput = true;
        $fileExists = true;
        $c->loadModels();

        $helperOutput = false;
        $fileExists = false;
        $c->loadModels();

        $fileExists = true;
        $c->models = false;
        $c->loadModels();
    }

    /*
     * Test Load Views
     *
     * Load views checks if it needs to load any views, if it needs to
     * load the view, and based on the "layout" property put it to output,
     * or load it as view data into the layout.
     */
    public function testLoadViews()
    {
        $c = $this->getMockBuilder("ControllerOverride")
            ->setMethods(array("_loadLanguage", "_callback", "_loadModels"))
            ->getMock();

        $c->view = "testView";
        $c->layout = false;
        $c->langFile = false;

        $loader = $this->getMockBuilder("ViewLoader")
            ->setMethods(array("loadView"))
            ->getMock();

        $loader->expects($this->exactly(3))
            ->method("loadView")
            ->withConsecutive(
                array($c->view, $c->viewData, true, true),
                array($c->view, $c->viewData, true, true),
                array("testLayout", array("mainView" => "testView Loaded"))
            )
            ->willReturn("testView Loaded");

        $c->setViewLoader($loader);

        $c->output = $this->getMockBuilder("Output")
            ->setMethods(array("set_output"))
            ->getMock();

        $c->output->expects($this->exactly(1))
            ->method("set_output")
            ->with("testView Loaded");

        // test view is loaded and put to output
        $this->expectOutputRegex("~testMethod~");
        $c->_remap("testMethod");

        // test view is loaded into the layout
        $c->layout = "testLayout";
        $c->_remap("testMethod");

        // test nothing is loaded when intended
        $c->view = false;
        $c->_remap("testMethod");
    }

    /*
     * Helper for CRUD testing
     *
     * All crud methods are tested similarly, combine tests in one method.
     */
    protected function _testCrud($method = "create")
    {
        $c = $this->getMockBuilder("\\SlaxWeb\\BaseController\\BaseController")
            ->setMethods(array("_loadLanguage", "_loadViews", "_callback", "_loadModels"))
            ->getMock();

        $ucMethod = ucfirst($method);
        $afterMethodView = "after{$ucMethod}View";
        $afterMethod = "after{$ucMethod}";
        $methodRules = "{$method}Rules";

        $crudMethod = "{$method}_post";
        $modelMethod = ($method === "create") ? "insert" : $method;

        $c->input->postData = array("test" => "data");
        $c->{$afterMethod} = $afterMethodView;
        $c->{$methodRules} = $methodRules;

        // Error object
        $error = new \stdClass;

        // Status mock object
        $status = $this->getMockBuilder("status")
            ->setMethods(array("error"))
            ->getMock();

        // Error mock method
        $status->expects($this->exactly(3))
            ->method("error")
            ->will($this->onConsecutiveCalls($error, false, $error));

        // Model mock object
        $c->TestController = $this->getMockBuilder("model")
            ->setMethods(array($modelMethod))
            ->getMock();

        // CRUD mock method
        $c->TestController->expects($this->exactly(3))
            ->method($modelMethod)
            ->with($c->input->postData)
            ->will($this->onConsecutiveCalls(true, $status, $status));

        // Test - everything ok
        $c->{$crudMethod}();
        $this->assertEquals($c->{$afterMethod}, $c->view);
        $this->assertEquals($c->viewData, array());
        $this->assertEquals($c->{$methodRules}, $c->TestController->rules);

        // Test - validation error
        $error->message = "Validation Error Message";
        $c->{$crudMethod}();
        $this->assertEquals($c->{$afterMethod}, $c->view);
        $this->assertEquals(array("{$method}Error" => "Validation Error Message"), $c->viewData);

        // Test - crud error
        $error->message = "{$ucMethod} Error Message";
        $c->{$crudMethod}();
        $this->assertEquals($c->{$afterMethod}, $c->view);
        $this->assertEquals(array("{$method}Error" => "{$ucMethod} Error Message"), $c->viewData);
    }
}
