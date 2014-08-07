Vario_Loader
==========

One of the biggest problem of the Zend Framework 1.x series was the fact that it was almost dead slow. It had no good autoloader and because of that it suffered severe performance hit.

Although Zend Framework 2.x autoloader was backported to 1.x series, it has not been very effective.

This autoloader uses several techniques to have performance gains more than 200% . When using additional optimizer, performance gains may be even more bigger.

1. Remove all "require_once" commands from Zend Framework libraries
2. Compile everything into one big file and update this file as neccessary. Loading one big file at once is much faster than looking for huge amount of smaller files.
3. Remove comments, spaces, everything that takes up space from the files to make compilation as small as possible
4. As an option, you can turn off compilation from .htaccess


I have been using this class for a while and performance gains are impressive. Especially when using additional optimizer , such as APC cache.

How to use?
--------------

In your index.php you have to setup constants and loader itself

defined('CLASSMAP_COMPILATION') || define('CLASSMAP_COMPILATION', (getenv('CLASSMAP_COMPILATION') ? getenv('CLASSMAP_COMPILATION') : false));
defined('SCRIPT_COMPILATION') || define('SCRIPT_COMPILATION', (getenv('SCRIPT_COMPILATION') ? getenv('SCRIPT_COMPILATION') : false));

require_once 'Vario/Loader.php';
Vario_Loader::initLoader();

Optionally you can also setup .htaccess:

SetEnv CLASSMAP_COMPILATION false
SetEnv SCRIPT_COMPILATION false

How to remove "require_once" from Zend Framework libraries
--------------

Just run "sh cleanzendframework.sh" from shell. This bash script takes care of this issue.