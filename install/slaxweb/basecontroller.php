<?php
/**
 * SlaxWeb BaseController config file
 */

/**
 * Convert controller class case:
 *
 * 0 - leave as it is
 * 1 - upper case first letter
 * 2 - everything to lower case
 */
$config["controller_class_case"] = 0;

/**
 * Autoload models
 *
 * Available values: true/false
 * Default: true
 */
$config["enable_model_autoload"] = true;

/**
 * Autoload views
 *
 * Available values: true/false
 * Default: true
 */
$config["enable_view_autoload"] = true;

/**
 * Template layout
 *
 * Available values: true/false
 * Default: true
 */
$config["enable_layout_autoload"] = false;

/**
 * Automatically load language file in every controller
 *
 * Available values: true/false
 */
$config["enable_language_autoload"] = true;

/**
 * Mandatory model
 *
 * If the model autoload is enabled, should each controller have an existing
 * model, or should missing models not throw an error?
 *
 * Available values: true/false
 * Default: false
 */
$config["mandatory_model"] = false;

/**
 * Default view
 *
 * The default view path inside your set view directory. This does not dictate
 * in which directory your views will be located. This is done through
 * CodeIgniter configuration. This only dictates directory structure of views
 * inside of your view directory.
 *
 * Path variables:
 * - {controllerDirectory} - replaced by the sub-directory the controller is in
 * - {controllerName} - replaced by the current controller name
 * - {methodName} - replaced by the current method name
 * 
 * Default: {controllerDirectory}{controllerName}/{methodName}/main
 */
$config["default_view"] = "{controllerDirectory}{controllerName}/{methodName}/main";

/**
 * Mandatory language
 *
 * If the language autoload is enabled, does each controller need an existing
 * language file, or should missing language files not throw an error?
 *
 * Available values: true/false
 */
$config["mandatory_language"] = true;
