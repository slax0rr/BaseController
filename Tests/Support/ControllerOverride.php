<?php

class ControllerOverride extends \SlaxWeb\BaseController\BaseController
{
    public function __construct()
    {
        $this->models = false;
        parent::__construct();
    }

    public function loadModels()
    {
        $this->_loadModels();
    }

    public function setViewLoader($loader)
    {
        $this->_viewLoader = $loader;
    }
}
