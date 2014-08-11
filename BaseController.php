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
     * Auto load the views
     *
     * @var bool
     */
    public $load = true;
    /**
     * View name
     *
     * If left empty it will load the view: "controller/method/main"
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
     * Header View
     *
     * @var string
     */
    public $head = "";
    /**
     * Footer View
     *
     * @var string
     */
    public $foot = "";
    /**
     * Include Header/Footer
     *
     * @var bool
     */
    public $include = true;
    /**
     * Include language in view data
     *
     * @var bool
     */
    public $includeLang = true;
    /**
     * Language file
     *
     * Use controller name as language file if not set
     *
     * @var string
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
     * View Loader object
     *
     * @var \SlaxWeb\ViewLoader\Loader
     */
    protected $_VH = null;

    /**
     * Initiate the view loader class
     */
    public function __construct()
    {
        parent::__construct();
        $this->_VH = new \SlaxWeb\ViewLoader\Loader($this);
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
            call_user_func_array(array($this, $method), $params);
            $this->_loadViews();
        } elseif (method_exists($this, "_404")) {
            call_user_func(array($this, "_404"));
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
        if ($this->load === false) {
            return true;
        }

        // If view is not set, try to load the default view for the method
        if ($this->view === "") {
            $this->view = strtolower("{$this->router->fetch_class()}/{$this->router->fetch_method()}/main");
        }

        // Are header and footer set? And are they to be included?
        if ($this->include === true && ($this->head !== "" || $this->foot !== "")) {
            $this->_VH->setHeaderView($this->head);
            $this->_VH->setFooterView($this->foot);
        }

        // Load language
        if ($this->includeLang === true) {
            // try to use controller name as language file name
            if ($this->langFile === "") {
                $this->langFile = $this->router->fetch_class();
            }

            // Use controller name as prefix if not set
            if ($this->langPrefix === "") {
                $this->langPrefix = $this->router->fetch_class() . "_";
            }

            // check if language file exists
            if (file_exists(APPPATH . "languages/{$this->language}/{$this->langFile}_lang.php") === true) {
                $this->lang->load($this->langFile, $this->language);
                $this->_VH->setLanguageStrings($this->langPrefix);
            }
        }

        // Load the sub-views
        if (empty($this->subViews) === false) {
            foreach ($this->subViews as $name => $view) {
                if (is_array($view) === true) {
                    $this->viewData["subview_{$name}"] = $this->_VH->loadView($view, $this->viewData, false, true);
                } else {
                    $this->viewData["subview_{$name}"] = $this->_VH->loadView($view["view"], $view["data"], false, true);
                }
            }
        }

        // We have everything, now just load the view
        $this->_VH->loadView($this->view, $this->viewData);
        return true;
    }
}
