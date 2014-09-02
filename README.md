BaseController
==============

Base controller for CodeIgniter, helps you with loading views, subviews, and populating them with data and loading of language as well as injecting said languages into the view data.

The idea for the BaseController came from Jamie Rumbelows [base controller](https://github.com/jamierumbelow/codeigniter-base-controller), with some additions and changes. At this point I would also like to thank [Marco Monteiro](https://github.com/mpmont) and [Sami Keinänen](https://github.com/skope) for their help.

If you run into issues or have questions/ideas, please submit a ticket here on [GitHub](https://github.com/slax0rr/BaseController/issues).

This is still in development phase, but is available for public as early-beta. Please use with caution, I can not guarantee that something will not change along the way.

Table of contents
=================
* [BaseController](https://github.com/slax0rr/BaseController/blob/develop/README.md#basecontroller)
* [Table of contents](https://github.com/slax0rr/BaseController/blob/develop/README.md#table-of-contents)
* [Install](https://github.com/slax0rr/BaseController/blob/develop/README.md#install)
* [View loading and data](https://github.com/slax0rr/BaseController/blob/develop/README.md#view-loading-and-data)
  * [Example](https://github.com/slax0rr/BaseController/blob/develop/README.md#example)
  * [Basic usage](https://github.com/slax0rr/BaseController/blob/develop/README.md#basic-usage)
  * [Disable view loading](https://github.com/slax0rr/BaseController/blob/develop/README.md#disable-view-loading)
  * [Change view file](https://github.com/slax0rr/BaseController/blob/develop/README.md#change-view-file)
  * [Load sub-views](https://github.com/slax0rr/BaseController/blob/develop/README.md#load-sub-views)
  * [View data](https://github.com/slax0rr/BaseController/blob/develop/README.md#view-data)
  * [Controller 404 page](https://github.com/slax0rr/BaseController/blob/develop/README.md#controller-404-page)
* [Languages](https://github.com/slax0rr/BaseController/blob/develop/README.md#languages)
  * [Example](https://github.com/slax0rr/BaseController/blob/develop/README.md#example-1)
  * [Basic usage](https://github.com/slax0rr/BaseController/blob/develop/README.md#basic-usage-1)
  * [No languages](https://github.com/slax0rr/BaseController/blob/develop/README.md#no-languages)
  * [Language file](https://github.com/slax0rr/BaseController/blob/develop/README.md#language-file)
  * [Language prefix](https://github.com/slax0rr/BaseController/blob/develop/README.md#language-prefix)
  * [Non-Default language](https://github.com/slax0rr/BaseController/blob/develop/README.md#non-default-language)
* [Templates](https://github.com/slax0rr/BaseController/blob/develop/README.md#templates)
  * [Example](https://github.com/slax0rr/BaseController/blob/develop/README.md#example-2)
  * [DEPRECATED - Setting template files](https://github.com/slax0rr/BaseController/blob/develop/README.md#deprecated---setting-template-files)
  * [DEPRECATED - Disable template](https://github.com/slax0rr/BaseController/blob/develop/README.md#deprecated---disable-template)
  * [Layout](https://github.com/slax0rr/BaseController/blob/develop/README.md#layout)
* [Manual view loading](https://github.com/slax0rr/BaseController/blob/develop/README.md#manual-view-loading)
* [ChangeLog](https://github.com/slax0rr/BaseController/blob/develop/README.md#changelog)

Install
=======

The easiest way to install at the moment is to use [composer](https://getcomposer.org/).
Simply create composer.json file in your project root:
```
{
  "require": {
    "slaxweb/ci-basecontroller": "0.2.0.*@dev"
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

Some controller methods do not load views. In this case set BaseController property **view** to false:
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

Controller 404 page
-------------------

If a controller method is not found, the Basecontroller will search in the routed-to controller a *_404* method and call it, so you can have custom 404 pages per controller. If it is not found, it will call the CodeIgniters *show_404* method, and the CodeIgniter 404 page will be displayed as per normal operation.

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

No languages
------------

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
If you wish to load multiple language files, set an array with those language file names to the **langFile** property.
```PHP
$this->langFile = array("Lang1", "Lang2", "Lang3");
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
  // DEPRECATED
  public function template()
  {
    $this->head = "head/view";
    $this->foot = "foot/view";
  }
  
  // DEPRECATED
  public function noTemplate()
  {
    // if a template is already loaded, you can disable it
    $this->include = false;
  }

  public function layout()
  {
    // to load a layout, set layout property to true
    // base controller will try to load the controllers layout if not found
    // it will load the application default layout
    $this->layout = true;
  }

  public function specificLayout()
  {
    // if you want your method or whole controller to have a specific
layout file you can set it to the layout property
    $this->layout = "layout/view";
  }
}
```

DEPRECATED - Setting template files
-----------------------------------

In order to set the header and/or footer files, properties **head**, and **foot** must be set. Then the header view will be loaded before the controller view(s), and the footer will come after.
```PHP
$this->head = "head/view";
$this->foot = "foot/view";
```

DEPRECATED - Disable template
-----------------------------

If you have set the template files, but would not like to display the header and footer views, you need to set the **include** property to false.
```PHP
$this->include = false;
```

Layout
------

Instead of header/footer files, BaseController now provides layouts. The
layout is the whole template and your controller view gets injected into
this layout view through the **mainView** variable. You have three
options:
* No layout (default), set BaseController **layout** property to false
* Controller specific layout, set property **layout** to true,
  BaseController will try to load the default controller layout file from {views}/layouts/ControllerDir/ControllerName/layout
* Application specific layout, set property **layout** to true, and make
  sure controller specific layout view file does not exists
* Custom layout, set the path to the layout view file to the **layout**
  property
```PHP
// use controller or application specific layout
$this->layout = true;
// use custom layout
$this->layout = 'layout/myLayout';
```

Manual view loading
===================

BasicController also allows you to manually load any view file you want. Because BaseController is using the [ViewLoader](https://github.com/slax0rr/ViewLoader), you can access it through the protected **_viewLoader** property. For help with using the ViewLoader, please read the readme [here](https://github.com/slax0rr/ViewLoader/blob/develop/README.md).

ChangeLog
=========

0.2.0 - develop
---------------

* Switch version numbers
* Add layout support

0.1.1.0
-------

* First beta release

0.1.0.3
------

* Multiple language files

0.1.0.2
-------

* Cast view path to lower-case before trying to load the guessed view file
* Include controller sub-dir in guessed view file

0.1.0.1
-------

* Do not check if a view file exists, but let CodeIgniter handle this.

0.1.0.0
-------

* Initial version
