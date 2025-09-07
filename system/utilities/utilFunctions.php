<?php

function findFile($path, $fileNamePart)
{
    $dir = opendir($path);
    $was_sent = false;
    while (($file = readdir($dir)) == true)
    {
        if ($file != "." && $file != ".." && strpos($file, $fileNamePart) !== false)
        {
            return $path.$file;
        }
    }
    closedir($dir);
    return false;
}    

?>
