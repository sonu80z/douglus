<?php
/*function __autoload($className) {
    loadClass($className);
}*/
spl_autoload_register('loadClass');
function loadClass($className) {
    global $imports;
    if (isset($imports[$className])) {
        include_once($imports[$className]);
    }
}
$imports = array();
function inc($path){
  include($_SERVER["DOCUMENT_ROOT"]."/".eregi_replace("[.]", "/", $path). ".php");
}
function import($import) {
    global $imports;
    // seperate import into a package and a class
		
    $lastDot = strrpos($import, '.');
    $class = $lastDot ? substr($import, $lastDot + 1) : $import;
    $package = substr($import, 0, $lastDot);
   	
	
    // if this import has already happened, return true
    if (isset($imports[$class]) || isset($imports[$package.'.*'])) return true;
   
    // create a folder path out of the package name
    $folder = $_SERVER["DOCUMENT_ROOT"]."/". ($package ? str_replace('.', '/', $package) : '');
    $file = "$folder/$class.php";
    
    // make sure the folder exists
    if (!file_exists($folder)) {
        $back = debug_backtrace();
        return trigger_error("There is no such package <strong>'$package'</strong> -- Checked folder <strong>'$folder'</strong><br />
            Imported from <strong>'{$back[0]['file']}'</strong> on line <strong>'{$back[0]['line']}'</strong><br />", E_USER_WARNING);
    } elseif ($class != '*' && !file_exists($file)) {
        $back = debug_backtrace();
        return trigger_error("There is no such Class <strong>'$import'</strong> -- Checked for file <strong>'$file'</strong><br />
            Imported from <strong>'{$back[0]['file']}'</strong> on line <strong>'{$back[0]['line']}'</strong><br />", E_USER_WARNING);
    }
    
    if ($class != '*') {
        // add the class and it's file location to the imports array
        $imports[$class] = $file;//$_SERVER["DOCUMENT_ROOT"]."/".$file;
    } else {
        // add all the classes from this package and their file location to the imports array
        // first log the fact that this whole package was alread imported
        $imports["$package.*"] = 1;
        $dir = opendir($folder);
        while (($file = readdir($dir)) !== false) {
            if (strrpos($file, '.php')) {
                $class = str_replace('.php', '', $file);
                // put it in the import array!
                $imports[$class] = "$folder/$file";
            }
        }
    }
}
?>