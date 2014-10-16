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
     * @var bool
     */
    public $include = true;
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
     * Language file
     *
     * Use controller name as language file if not set. Can be set with either one language file in a string
     * or multiple files in an array. If set to false, no language file is used.
     *
     * @var mixed
     */
    public $langFile = true;
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
     * Additional language prefix
     *
     * If you need more than one prefix, you can set it to this array.
     *
     * @var array
     */
    public $additionalPrefixes = array();
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

    /*************************
     * Basic CRUD properties *
     *************************/
    /**
     * Create part of CRUD validation rules
     *
     * @var array
     */
    public $createRules = array();
    /**
     * View for after Create
     *
     * If left empty the default "create" method view will be used.
     *
     * @var string
     */
    public $afterCreate = "";
    /**
     * Update part of CRUD validation rules
     *
     * @var array
     */
    public $updateRules = array();
    /**
     * View for after Update
     *
     * If left empty the default "update" method view will be use.
     *
     * @var string
     */
    public $afterUpdate = "";
    /**
     * View for after Delete
     *
     * If left empty the default "delete" method view will be use.
     *
     * @var string
     */
    public $afterDelete = "";

    /*************
     * Callbacks *
     *************/
    public $beforeLanguage = "";
    public $afterLanguage = "";

    public $beforeMethod = "";
    public $afterMethod = "";

    public $beforeModel = "";
    public $afterModel = "";


    /**
     * Initiate the view loader class
     */
    public function __construct()
    {
        parent::__construct();
        $this->_viewLoader = new \SlaxWeb\ViewLoader\Loader($this);
        $this->_loadModels();
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
        $this->_loadLanguage();
        $method .= ($this->input->server("REQUEST_METHOD") === "POST") ? "_post" : "";
        if (method_exists($this, $method)) {
            $this->_method = $this->router->fetch_method();

            $this->_callback($this->beforeMethod);

            call_user_func_array(array($this, $method), $params);

            $this->_callback($this->afterMethod);

            $this->_loadViews();
        } elseif (method_exists($this, "_404")) {
            $this->_method = "_404";
            call_user_func(array($this, "_404"));
            $this->_loadViews();
        } else {
            show_404();
        }
    }

    /****************************
     * Basic CRUD functionality *
     ****************************/
    /**
     * Retrieve method
     *
     * Retrieve part of crud, executed automatically.
     * Method retrieves all parameters from its respective models table, and
     * injects them into the view data.
     */
    public function index($id = 0)
    {
        $model = $this->router->fetch_class();
        $data = $this->{$model}->get($id);
        $this->viewData = array_merge($this->viewData, array("_tableData" => $data));
    }

    /**
     * Create method
     *
     * Create part of crud, executed automatically.
     * Method is auto accessed through POST request method.
     */
    public function create_post()
    {
        $data = $this->input->post();
        $model = $this->router->fetch_class();
        $this->{$model}->rules = $this->createRules;
        $status = $this->{$model}->insert($data);

        $this->view = $this->afterCreate;

        if ($status !== true) {
            if ($error = $status->error("VALIDATION_ERROR")) {
                $this->viewData = array("createError" => $error->message);
                return;
            } elseif ($error = $status->error("CREATE_ERROR")) {
                $this->viewData = array("createError" => $error->message);
                return;
            }
        }
    }

    /**
     * Update method
     *
     * Update part of crud, executed automatically.
     * Method is accessed through POST request method,
     * and updates all the parameters found in the POST array.
     */
    public function update_post($id = 0)
    {
        $data = $this->input->post();
        $model = $this->router->fetch_class();
        $this->{$model}->rules = $this->updateRules;
        $status = $this->{$model}->update($data, $id);

        $this->view = $this->afterUpdate;

        if ($status !== true) {
            if ($error = $status->error("VALIDATION_ERROR")) {
                $this->viewData = array("updateError" => $error->message);
                return;
            } elseif ($error = $status->error("UPDATE_ERROR")) {
                $this->viewData = array("updateError" => $error->message);
                return;
            }
        }
    }

    /**
     * Delete method
     *
     * Delete part of crud, executed automatically.
     * Method is accessed through POST request method,
     * and deletes the record from the database.
     *
     * Even though the request has to be a post method, the ID to be deleted
     * still has to be sent as normal parameter.
     */
    public function delete_post($id = 0)
    {
        $model = $this->router->fetch_class();
        $status = $this->{$model}->delete($id);

        $this->view = $this->afterDelete;

        if ($status !== true) {
            if ($status === false) {
                $this->viewData = array("deleteError" => $this->lang->line("error_delete_generic"));
                return;
            } elseif ($error = $status->error("UPDATE_ERROR")) {
                $this->viewData = array("deleteError" => $error->message);
                return;
            }
        }
    }

    /**
     * Autoload model
     *
     * Try to load the guessed model, if allowed. If property "models" is an array
     * try to load those models as well.
     */
    protected function _loadModels()
    {
        $this->_callback($this->beforeModel);

        if (isset($this->models) === false || $this->models !== false) {
            $model = ucfirst("{$this->router->fetch_class()}_model");
            if (file_exists(APPPATH . "models/{$model}.php")) {
                $this->load->model($model, $this->router->fetch_class());
            }

            if (isset($this->models) === true && is_array($this->models)) {
                foreach ($this->models as $m) {
                    $this->load->model("{$m}_model", $m);
                }
            }
        }

        $this->_callback($this->afterModel);
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
        if ($this->langFile !== false) {
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
        // Use controller name as prefix if not set
        if ($this->langPrefix === "") {
            $this->langPrefix = strtolower($this->_method) . "_";
        }

        $this->_viewLoader->setLanguageStrings($this->langPrefix);

        // check if we should include more than the on prefix
        if (empty($this->additionalPrefixes) === false) {
            foreach ($this->additionalPrefixes as $prefix) {
                $this->_viewLoader->setLanguageStrings($prefix);
            }
        }
    }

    /**
     * Load language file(s)
     *
     * Load them before the execution of controller method, so they can be used
     * in the controller method as well. Only loads the language files.
     */
    protected function _loadLanguage()
    {
        $this->_callback($this->beforeLanguage);

        // try to use controller name as language file name
        if ($this->langFile === true) {
            $this->langFile = $this->router->fetch_class();
        }

        if (is_string($this->langFile) === true) {
            $this->lang->load($this->langFile, $this->language);
        } elseif (is_array($this->langFile) === true) {
            foreach ($this->langFile as $lang) {
                $this->lang->load($lang, $this->language);
            }
        }

        $this->_callback($this->afterLanguage);
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

    protected function _callback($callback)
    {
        $call = false;
        if (is_array($callback)) {
            if (method_exists($callback[0], $callback[1])) {
                $call = true;
            }
        } elseif (function_exists($callback)) {
            $call = true;
        }

        if ($call === true) {
            call_user_func($callback);
        }
    }
}
