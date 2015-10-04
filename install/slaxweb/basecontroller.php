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
 * Autoload views
 *
 * Available values: true/false
 */
$config["autoload_view"] = true;

/**
 * Template layout
 *
 * Available values:
 * - false - do not use layouts
 * - true - use a layout, and try to obtain the controller layout first,
 * or use the application default layout
 * - path/to/layout - path to a custom layout view
 */
$config["load_layout"] = false;

/**
 * Automatically load language file in every controller
 *
 * Available values: true/false
 */
$config["autoload_language"] = true;

/**
 * Autoload models
 *
 * Available values: true/false
 */
$config["autoload_model"] = true;
