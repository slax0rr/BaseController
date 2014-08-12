BaseController
==============

Base controller for CodeIgniter, helps you with loading views, subviews, and populating them with data and loading of language as well as injecting said languages into the view data.

Install
-------

The easiest way to install at the moment is to use [composer](https://getcomposer.org/).
Simply create composer.json file in your project root:
```
{
  "require": {
    "slaxweb/ci-basecontroller": "0.1.0.*@dev"
  }
}
```

Then run **composer.phar install**. When finished, edit CodeIgniter index.php file and add this line right after PHP opening tag:
```PHP
require_once "vendor/autoload.php";
```

Congratulations, BaseController is installed.

View loading and data
=====================

Example
-------
```PHP
<?php
class Contrlr extends \SlaxWeb\BaseController\BaseController
{
  public function showView()
  {
    // nothing, that's enough
    // base controller will load application/views/contrlr/showview/main.php view file
    // if the controller is located in a sub-dir, sub-dir is also included in view path
  }
  
  public function nonDefaultView()
  {
    $this->view = "path/to/different/view/file";
  }
  
  public function noView()
  {
    $this->view = false;
    // do whatever you want
  }
  
  public function subView()
  {
    $this->subViews = array("varName" => "subview/file");
  }
  
  public function viewWithData()
  {
    $this->viewData = array("name" => "value");
  }
}
```

Basic usage
-----------

To start using, all your controllers must extend **\SlaxWeb\BaseController\BaseController** instead of CI_Controller. If you are already extending MY_Controller, then extend **\SlaxWeb\BaseController\BaseController** in MY_Controller.

And this is it, after a controller method is done executing, BaseController will automatically load the view file associated with this controller method. The default view being loaded is: *application/views/(controllerdir)/controllername/controllermethod/main*.

Disable view loading
--------------------

Some controller methods do not load views, in this case set BaseController property **view** to false:
```PHP
$this->view = false
```

Change view file
----------------

Want to load a different view file and not the default one? No problem, just set the desired view file to **view** property.
```PHP
$this->view = "desired/view";
```

Load sub-views
--------------

If you need to load subviews into your main view, you can do so, by assigning an array to the BaseController **subViews** property. The key name in the array is later used as the var in the main view, and the value in the array is the sub-view file name. Controller code:
```PHP
$this->subViews = array("name" => "subview/file");
```

Main view:
```PHP
<?php echo $subview_name; ?>
```

View data
---------

To load data into view, simply assign it to the **viewData** property array.
```PHP
$this->viewData = array("name" => "value");
```

Languages
=========

BaseController also autoloads language files and loads properly prefixed language strings into the view data before loading the view. Language filename must have the same name as the controller, and all language strings that will be injected into the view data, need to have *methodname_* prefix in the language string key.

Example
-------

```PHP
<?php
class Contrlr extend \SlaxWeb\BaseController\BaseController
{
  public function injectLanguage()
  {
    // you're done, default view is loaded with default language and its prefixed language strings.
  }
  
  public function noLang()
  {
    $this->includeLang = false;
  }
  
  public function diffLangFile()
  {
    $this->langFile = "Diffrent_lang";
  }
  
  public function diffPrefix()
  {
    $this->langPrefix = "custom_prefix_";
  }
  
  public function nonDefaultLanguage()
  {
    $this->language = "german";
  }
}
```
Basic usage
-----------

By default, base controller will auto-load the language file that has the same name as the controller name, from the default language directory. By default that is english, changeable in CodeIgniter config. By default it will load all language strings which have the *methodname_* as the prefix in the language string key name.
```PHP
class Contrlr \SlaxWeb\BaseController\BaseController
{
  public function defaultLang()
  {
  }
}
```

Above will try to autoload **application/language/english/Contrlr_lang.php** and inject translated strings.
```PHP
$lang["defaultLang_var1"] = "string";
$lang["defaultLang_var2"] = "string";
$lang["defaultLang_var3"] = "string";
```

In the view, vars **$var1**, **$var2**, and **$var3**, will be available.

... TO BE CONTINUED ...
