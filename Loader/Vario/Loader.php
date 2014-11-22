<?php
 /**
 * Vario_Loader
 *
 * @description Autoloader and automatic class map generator for Zend Framework 1.x
 * @version 1.0 
 * @author Henry Algus <henryalgus@gmail.com>
 *
 */

class Vario_Loader
{
   
	/**
	 * Loads class map or generates new
	 * @param (string) $class class name
	 */
	
	public static function loadClassMap($class) {
		
		static $map = array();
		if (empty ( $map )) {
			// does map already exist?
		    if(is_file(CLASSMAP_CACHE)) {
			    $map = include CLASSMAP_CACHE;
		    } else {
		        $map = array();
		    }
		}

		// class already exists, nothing to do
		if (isset ( $map [$class] )) {
			require  $map [$class];
		} else {
			// convert class name into loadable format
			$className = str_replace("_","/",$class);
			$mapFile = realpath(APPLICATION_PATH . '/../library/' . $className.".php");
			require_once $mapFile;
			// generate new map..
			$currentMap = null;
			if(is_file(CLASSMAP_CACHE)) {
			    $currentMap = include CLASSMAP_CACHE;
			}		

			// nothing yet ?
			if(!is_array($currentMap)) {
			    $currentMap = array();
			}		
			// merge all files together
			$currentMap = array_merge($currentMap, array($class => $mapFile));
			Vario_Loader::generateClassMap($currentMap,(SCRIPT_COMPILATION == 'true')?true:false);
		}
	}
	
	/**
	 * Create class map as an array and optionally compile everything into one file
	 * @param array $currentMap current map as an array
	 * @param bool $compile compile everything into one file
	 */
	public static function generateClassMap($currentMap,$compile = false) {
			$mapCount = count($currentMap);
			$mapCode = '<?php return array(';
			$i = 1;
			$compilation = "";
			foreach($currentMap as $key =>$mapItem) {
				$addSep = ($mapCount > $i)?',':'';
				$mapCode .= "'".$key."' => '".$mapItem."'".$addSep."\n";
				if($compile) {
					$compilation .= "\n\nif (!class_exists('".$key."') && !interface_exists('".$key."')) {\n";
					$compilation .= file_get_contents($mapItem);
					$compilation .= "\n}";
				}
				$i++;
			}
			$mapCode .= ");";
			
			$handle = fopen(CLASSMAP_CACHE, 'w');
			if($handle) {
				fwrite($handle,$mapCode);
				fclose($handle);
			}
			// compile ?		
			if($compile) {
				Vario_Loader::generateCompilation($compilation);
			}
	}
	
	/**
	 * Removes all comments, newlines, whitespaces from php source
	 * @param string $compilation php source
	 */
	public static function generateCompilation($compilation) {
			set_time_limit(0);
			$tokens = token_get_all($compilation);  
	 		$compilation = "";
	 		// remove all data that is "garbage" for compiler
			foreach ($tokens as $token)  {			    
			   if (is_string($token)) {
                    $compilation .= $token;
               } else {
                    switch ($token[0]) {
                    case T_COMMENT:
                    case T_DOC_COMMENT:
                    case T_OPEN_TAG:
                    case T_CLOSE_TAG:
                    break;
                    case T_WHITESPACE:
                    $compilation .= " ";
                    break;
                    default:
                    $compilation .= $token[1];
                    }
              }		    
			} 
			// open cache and write everyting to disk
			$handle = fopen(COMPILATION_CACHE, 'w');
			if($handle) {
				// remove starting tags
				fwrite($handle, "<?php ".str_replace("<?php","",$compilation));
				fclose($handle);
			}		
	}
	
	/**
	 * Sets Vario_Loader as default autoloader and includes compilation files, if available
	 * This currently works only with Zend_Application
	 */
	public static function initLoader() {
		// compilation is enabled, lets begin
		if(CLASSMAP_COMPILATION == 'true') {
			// if script compilation is also enabled, lets include current cache (if available)
			if(SCRIPT_COMPILATION == 'true') {
			    if(is_file(COMPILATION_CACHE)) {
			       require_once COMPILATION_CACHE;  
			    } 
			}	
		    require_once 'Zend/Loader/Autoloader.php';
		    $loader = Zend_Loader_Autoloader::getInstance();
		    $loader->setDefaultAutoloader('Vario_Loader::loadClassMap');
		} else {
			// @TODO make this more dynamic and flexible
			if(!class_exists('Zend_Application')) {
		    	require_once 'Zend/Application.php';
			}
		}
	}
}
