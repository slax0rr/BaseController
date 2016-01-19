<?php
/**
 * Config Loader and Parser
 *
 * Loads the config and parses the config values, and makes them available to
 * the BaseController
 *
 * @category Config
 * @package  SlaxWeb\BaseController
 * @author   Tomaz Lovrec <tomaz.lovrec@gmail.com>
 * @license  MIT <https://opensource.org/licenses/MIT>
 */
namespace SlaxWeb\BaseController\Config;

class Loader
{
    /**
     * CodeIgniter instance
     *
     * @var object
     */
    protected $_ciInstance = null;

    /**
     * Automatically load model
     *
     * @var bool
     */
    protected $_autoModel = true;

    /**
     * Each controller requires a model with the same name
     *
     * @var bool
     */
    protected $_mandatoryModel = true;

    /**
     * Autoload view
     *
     * @var bool
     */
    protected $_loadView = true;

    /**
     * Default view path
     *
     * @var string
     */
    protected $_defaultView =
        "{controllerDirectory}{controllerName}/{methodName}/main";

    /**
     * Autoload layout
     *
     * @var bool
     */
    protected $_loadLayout = true;

    /**
     * Autoload language
     *
     * @var bool
     */
    protected $_loadLang = true;

    /**
     * Each controller must have a language file with the same name
     *
     * @var bool
     */
    protected $_mandatoryLang = true;

    /**
     * Language
     *
     * Use default language if not set
     *
     * @var string
     */
    protected $_language = "";

    /**
     * Construct the config loader class, load the config file and set the
     * CodeIgniter instance to the class property.
     *
     * @param object $ciInstance CodeIgniter instance
     * @author Tomaz Lovrec <tomaz.lovrec@gmail.com>
     */
    public function __construct($ciInstance)
    {
        // load the config file
        $ciInstance->load->config("slaxweb/basecontroller");

        $this->_ciInstance = $ciInstance;

        // parse the config
        $this->_parseConfig();
    }

    /**
     * Set protected property value
     *
     * Will try to set the retrieved value to the protected property.
     * If the property is not found, an error of type E_USER_ERROR is triggered.
     *
     * @param string $property Name of the protected property without leading
     *                         underscore.
     * @param mixed $value Value to be assigned to the protected property.
     * @return void
     * @author Tomaz Lovrec <tomaz.lovrec@gmail.com>
     */
    public function __set($property, $value)
    {
        $name = "_{$property}";
        if (isset($this->{$name}) === false) {
            $message = __CLASS__ . " has no property named '{$property}'";
            \log_message("error", "{$message} (500)");
            show_error($message, 500);
            return;
        }

        $this->{$name} = $value;
    }

    /**
     * Get protected property value
     *
     * Tries to retrieve the value of the protected property and returns it.
     * If the property is not found, an error of type E_USER_ERROR is triggered,
     * and the null value is returned.
     *
     * @param string $property Protected property value without leading
     *                         underscore.
     * @return mixed Value of the property, or null if not found
     * @author Tomaz Lovrec <tomaz.lovrec@gmail.com>
     */
    public function __get($property)
    {
        $name = "_{$property}";
        if (isset($this->{$name}) === false) {
            $message = __CLASS__ . " has no property named '{$property}'";
            \log_message("error", "{$message} (500)");
            show_error($message, 500);
            return null;
        }

        return $this->{$name};
    }

    /**
     * Parse config values
     *
     * Load the config values from the config file, and do some preliminary
     * checks on those values. On any error, log it, and revert config to its
     * default value.
     *
     * @return void
     * @author Tomaz Lovrec <tomaz.lovrec@gmail.com>
     */
    protected function _parseConfig()
    {
        // set model config values
        $this->_autoModel = $this->_ciInstance->config->item("enable_model_autoload");
        if (is_bool($this->_autoModel) === false) {
            \log_message("error", "Model autoload config value needs to be bool.");
            $this->_autoModel = true;
        }
        $this->_mandatoryModel = $this->_ciInstance->config->item("mandatory_model");
        if (is_bool($this->_mandatoryModel) === false) {
            \log_message("error", "Mandatory model config value type needs to be bool.");
            $this->_mandatoryModel = false;
        }

        // set view config values
        $this->_loadView = $this->_ciInstance->config->item("enable_view_autoload");
        if (is_bool($this->_loadView) === false) {
            \log_message("error", "View autoload config value needs to be bool.");
            $this->_loadView = true;
        }
        $this->_defaultView = $this->_ciInstance->config->item("default_view");
        if (empty($this->_defaultView)) {
            $this->_defaultView = "{controllerDirectory}{controllerName}/{methodName}/main";
        }

        // set layout config values
        $this->_loadLayout = $this->_ciInstance->config->item("enable_layout_autoload");
        if (is_bool($this->_loadLayout) === false) {
            \log_message("error", "Layout autoload config value needs to be bool.");
            $this->_loadLayout = true;
        }

        // set language config values
        $this->_loadLang = $this->_ciInstance->config->item("enable_language_autoload");
        if (is_bool($this->_loadLang) === false) {
            \log_message("error", "Lang autoload config value needs bo be bool.");
            $this->_loadLang = true;
        }
        $this->_mandatoryLang = $this->_ciInstance->config->item("mandatory_language");
        if (is_bool($this->_mandatoryLang) === false) {
            \log_message("error", "Mandatory language config value needs to be bool");
            $this->_mandatoryLang = true;
        }

        // set default language
        $this->_language = $this->config->item("language");
    }
}
