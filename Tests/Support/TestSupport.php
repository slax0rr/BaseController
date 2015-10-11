<?php
/**
 * Object registry
 */
class Registry
{
    protected static $_container = array();

    public static function get($class)
    {
        if (isset(self::$_container[$class]) === false) {
            self::$_container[$class] = new $class;
        }

        return self::$_container[$class];
    }
}

/**
 * Mock CI_Controller class
 */
class CI_Controller
{
    public $load = null;
    public $config = null;
    public $router = null;
    public $input = null;

    public function __construct()
    {
        $this->load = Registry::get("CI_Loader");
        $this->config = Registry::get("CI_Config");
        $this->router = Registry::get("CI_Router");
        $this->input = Registry::get("CI_Input");
    }

    public function _404()
    {
        echo "custom_404";
    }

    public function testMethod()
    {
        echo "testMethod";
    }

    /*
     * Callback output
     *
     * Helper method to check if callback gets called when it exists.
     */
    public function testCallbackOutput()
    {
        echo "Callback called\n";
    }
}

/**
 * Mock CI_Loader class
 */
class CI_Loader
{
    public function __call($name, $params)
    {
        return true;
    }
}

/**
 * Mock CI_Config class
 */
class CI_Config
{
    public $config = array(
        "controller_class_case"     =>  0,
        "enable_model_autoload"     =>  true,
        "enable_view_autoload"      =>  true,
        "enable_layout_autoload"    =>  false,
        "enable_language_autoload"  =>  false,
        "mandatory_model"           =>  false,
        "default_view"              =>  "{controllerDirectory}{controllerName}/{methodName}/main",
        "mandatory_language"        =>  false
    );

    public function __call($name, $params)
    {
        if ($name === "item") {
            return $this->config[$params[0]];
        }
        return 1;
    }
}

/**
 * Mock CI_Router class
 */
class CI_Router
{
    public $class = "TestController";
    public $method = "testMethod";
    public $directory = "";

    public function fetch_class()
    {
        return $this->class;
    }

    public function fetch_method()
    {
        return $this->method;
    }

    public function fetch_directory()
    {
        return $this->directory;
    }
}

/**
 * Mock CI_Input class
 */
class CI_Input
{
    public $server = array(
        "REQUEST_METHOD"    => "GET"
    );
    public $postData = array();


    public function server($key)
    {
        return $this->server[$key];
    }

    public function post($name = "")
    {
        if ($name === "") {
            return $this->postData;
        }
        if (isset($this->postData[$name])) {
            return $this->postData[$name];
        } else {
            return null;
        }
    }
}
