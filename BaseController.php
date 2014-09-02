<?php
namespace SlaxWeb\BaseController;

/**
 * CodeIgniter Base Controller
 *
 * Base controller makes view loading easier using the ViewLoader Loader class.
 *
 * @author Tomaz Lovrec <tomaz.lovrec@gmail.com>
 */
class BaseController extends \CI_Controller
{
    /**
     * View name
     *
     * If left empty it will load the view: "controller/method/main", set to false to disable loading
     *
     * @var string
     */
    public $view = "";
    /**
     * View data
     *
     * @var array
     */
    public $viewData = array();
    /**
     * SubViews
     *
     * Key name is the name of the variable in main view. Value is the name
     * of the view to be loaded. If special data needs to be injected to
     * the subview, the value can be a sub-array consisting of the view data
     * as array, and a string as the view name. Keys in sub-array need to be
     * "view" and "data".
     *
     * @var array
     */
    public $subViews = array();
    /**
     * Include Header/Footer
     *
     * DEPRECATED!
     *
     * @var bool
     */
    public $include = true;
    /**
     * Header View
     *
     * DEPRECATED!
     *
     * @var string
     */
    public $head = "";
    /**
     * Footer View
     *
     * DEPRECATED!
     *
     * @var string
     */
    public $foot = "";
    /**
     * Layout
     *
     * If set to false (default) the layout will not be used, if set to true,
     * BaseController will try to guess the default layout file for current
     * controller (layouts/ControllerDir/ControllerName/layout),
     * or you can also set you layout file manually into this property.
     *
     * @var mixed
     */
    public $layout = false;
    /**
     * Include language in view data
     *
     * @var bool
     */
    public $includeLang = true;
    /**
     * Language file
     *
     * Use controller name as language file if not set. Can be set with either one language file in a string
     * or multiple files in an array.
     *
     * @var mixed
     */
    public $langFile = "";
    /**
     * Language
     *
     * Use default language if not set
     *
     * @var string
     */
    public $language = "";
    /**
     * Language file prefix
     *
     * Include prefixed keys in view data. If no prefix is set,
     * controller name is used as prefix.
     *
     * @var string
     */
    public $langPrefix = "";
    /**
     * Controller method
     *
     * @var string
     */
    protected $_method = "";
    /**
     * View Loader object
     *
     * @var \SlaxWeb\ViewLoader\Loader
     */
    protected $_viewLoader = null;

    /**
     * Initiate the view loader class
     */
    public function __construct()
    {
        parent::__construct();
        $this->_viewLoader = new \SlaxWeb\ViewLoader\Loader($this);
    }

    /**
     * Remap function
     *
     * Call the method if it exists, if a custom 404 method exists, call it.
     * In other case, load the default 404 page.
     * After a successful method call, load the views.
     */
    public function _remap($method, $params = array())
    {
        if (method_exists($this, $method)) {
            $this->_method = $this->router->fetch_method();
            call_user_func_array(array($this, $method), $params);
            $this->_loadViews();
        } elseif (method_exists($this, "_404")) {
            $this->_method = "_404";
            call_user_func(array($this, "_404"));
            $this->_loadViews();
        } else {
            show_404();
        }
    }

    /**
     * Load the views
     *
     * After the controller method is done executing, load the views.
     */
    protected function _loadViews()
    {
        // should we load the views?
        if ($this->view === false) {
            return true;
        }

        // If view is not set, try to load the default view for the method
        if ($this->view === "") {
            $this->_setView();
        }

        /**
         * DEPRECATED
         */
        // Are header and footer set? And are they to be included?
        if ($this->include === true && ($this->head !== "" || $this->foot !== "")) {
            $this->_setTemplate();
        }

        // Load language
        if ($this->includeLang === true) {
            $this->_setLanguage();
        }

        // Load the sub-views
        if (empty($this->subViews) === false) {
            $this->_setSubviews();
        }

        // We have everything, now load the main view
        $data["mainView"] = $this->_viewLoader->loadView($this->view, $this->viewData, true, true);

        // If layout set to true, guess the default name of the layout
        if ($this->layout === true) {
            $this->_setLayout();
        }

        // If there is no layout, set everything loaded to this point to output
        if ($this->layout === false) {
            $this->output->set_output($data["mainView"]);
        } else {
            // Load the layout
            $this->_viewLoader->loadView($this->layout, $data);
        }
        return true;
    }

    /**
     * Set the view
     */
    protected function _setView()
    {
        $this->view = strtolower(
            "{$this->router->fetch_directory()}{$this->router->fetch_class()}/{$this->_method}/main"
        );
    }

    /**
     * Set the header and footer views
     *
     * !DEPRECATED!
     */
    protected function _setTemplate()
    {
        $this->_viewLoader->setHeaderView($this->head);
        $this->_viewLoader->setFooterView($this->foot);
    }

    /**
     * Set language strings
     *
     * Load the language file, and inject language strings with specific prefixes
     * in their keys.
     */
    protected function _setLanguage()
    {
        // try to use controller name as language file name
        if ($this->langFile === "") {
            $this->langFile = $this->router->fetch_class();
        }

        // Use controller name as prefix if not set
        if ($this->langPrefix === "") {
            $this->langPrefix = strtolower($this->_method) . "_";
        }

        if (is_string($this->langFile) === true) {
            $this->lang->load($this->langFile, $this->language);
        } elseif (is_array($this->langFile) === true) {
            foreach ($this->langFile as $lang) {
                $this->lang->load($lang, $this->language);
            }
        }
        $this->_viewLoader->setLanguageStrings($this->langPrefix);
    }

    /**
     * Load the subviews
     *
     * Load them away and add them to view data
     */
    protected function _setSubviews()
    {
        foreach ($this->subViews as $name => $view) {
            if (is_array($view) === true) {
                $this->viewData["subview_{$name}"] =
                    $this->_viewLoader->loadView($view["view"], $view["data"], false, true);
            } else {
                $this->viewData["subview_{$name}"] =
                    $this->_viewLoader->loadView($view, $this->viewData, false, true);
            }
        }
    }

    /**
     * Set the layout
     *
     * Try to obtain the layout, if not exists, set up the default one
     */
    protected function _setLayout()
    {
        $this->layout = strtolower(
            "layouts/{$this->router->fetch_directory()}{$this->router->fetch_class()}/layout"
        );
        if (file_exists(VIEWPATH . $this->layout . ".php") === false) {
            $this->layout = "layouts/default";
        }
    }
}
