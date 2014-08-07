<?php
 /**
 *
 * Index.php
 *
 * @description Basic example how to setup autoloader and autoloader constants.
 * @version 1.0 
 * @copyright 2014 Henry ALgus. All rights reserved.
 *
 */

if ( version_compare( phpversion(), '5.2' ) < 1 )
{
   echo  "<h1>Unsupported PHP version " . phpversion() . "</h1>" ;
   die();
}

defined('ROOT_PATH')
    || define('ROOT_PATH', realpath(dirname(__FILE__) . '/../'));
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

defined('PUBLIC_PATH')
    || define('PUBLIC_PATH', realpath(dirname(__FILE__)));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
    
// Define application environment
defined('CLASSMAP_COMPILATION')
    || define('CLASSMAP_COMPILATION', (getenv('CLASSMAP_COMPILATION') ? getenv('CLASSMAP_COMPILATION') : false));
    
// Define application environment
defined('SCRIPT_COMPILATION')
    || define('SCRIPT_COMPILATION', (getenv('SCRIPT_COMPILATION') ? getenv('SCRIPT_COMPILATION') : false));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

define('CLASSMAP_CACHE',APPLICATION_PATH . '/../data/cache/classMapCache'.ucfirst(APPLICATION_ENV).'.php');
define('COMPILATION_CACHE',APPLICATION_PATH . '/../data/cache/compilationCache'.ucfirst(APPLICATION_ENV).'.php');

require_once 'Vario/Loader.php';

// init loader here
Vario_Loader::initLoader();

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV, 
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();
