<?php

class ControllerOverride extends \SlaxWeb\BaseController\BaseController
{
    public function __construct() { }

    public function delayedConstruct()
    {
        parent::__construct();
    }

    public function __get($param)
    {
        if (isset($this->{$param})) {
            return $this->{$param};
        } else {
            return null;
        }
    }

    public function loadModels()
    {
        $this->_loadModels();
    }

    public function setViewLoader($loader)
    {
        $this->_viewLoader = $loader;
    }

    public function loadConfig()
    {
        $this->_loadConfig();
    }
}
