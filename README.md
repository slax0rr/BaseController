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
    $this->langFile = "Diffrent";
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

No language file
----------------

To disable loading of languages, simply disable it by setting property **includeLang** to false.
```PHP
$this->includeLang = false;
```

Language file
-------------

In order to load a different language file, set the **langFile** property.
```PHP
$this->langFile = "Different";
```

Language prefix
---------------
To change the prefix from the default method name, set the **langPrefix** property.
```PHP
$this->langPrefix = "langprefix_";
```

Non-Default language
--------------------
If you want to load a non-default language, you have to set it with the **language** property.
```PHP
$this->language = "german";
```

Templates
=========

Base controller also supports basic templating. At the moment only by setting a header and footer view.

Example
-------

```PHP
class Contrlr extends \SlaxWeb\BaseController\BaseController
{
  public function template()
  {
    $this->head = "head/view";
    $this->foot = "foot/view";
  }
  
  public function noTemplate()
  {
    // if a template is already loaded, you can disable it
    $this->include = false;
  }
}
```

Setting template files
----------------------

In order to set the header and/or footer files, properties **head**, and **foot** must be set. Then the header view will be loaded before the controller view(s), and the footer will come after.
```PHP
$this->head = "head/view";
$this->foot = "foot/view";
```

Disable template
----------------

If you have set the template files, but would not like to display the header and footer views, you need to set the **include** property to false.
```PHP
$this->include = false;
```

Manual view loading
===================

BasicController also allows you to manually load any view file you want. Because BaseController is using the [ViewLoader](https://github.com/slax0rr/ViewLoader), you can access it through the protected **_viewLoader** property. For help with using the ViewLoader, please read the readme [here](https://github.com/slax0rr/ViewLoader/blob/develop/README.md).

ChangeLog
=========

0.1.0.0
-------

Initial version
