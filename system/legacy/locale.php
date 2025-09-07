<?php
//
// locale.php
//
// Module for localization of php scripts for PacsOne Server
//
// CopyRight (c) 2003-2008 RainbowFish Software
//
// case insensitive local table
//this will turn error reporting off for this page.
error_reporting(0);
$LOCALE_TBL = array(
    "zh-tw"         => array("zh-TW", "big5"),
    "es-ar"         => array("es_AR", "UTF-8"),
    "fr-FR"         => array("fr-FR", "UTF-8"),
    "pl-PL"         => array("pl-PL", "UTF-8"),
    "it-IT"         => array("it-IT", "UTF-8"),
    "pt-BR"         => array("pt-BR", "UTF-8"),
    "ru-RU"         => array("ru-RU", "UTF-8"),
);
$locale = "";
// check the browser agent first
if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $tokens = explode(",", $_SERVER['HTTP_ACCEPT_LANGUAGE']);
    $locale = $tokens[0];
}
// check the browser URL next
else if (isset($_GET['locale'])) {
    $locale = $_GET['locale'];
}
//if (strlen($locale)) {
//    $key = strtolower($locale);
//    $charset = "";
//    if (isset($LOCALE_TBL[$locale])) {
//        $locale = $LOCALE_TBL[$key][0];
//        if (!extension_loaded('gettext')) {
//            print "<h3><font color=red>";
//            print "'gettext' PHP extension is requied for this locale: [$locale]";
//            print "</h3></font>";
//            exit();
//        }
//        $charset = $LOCALE_TBL[$key][1];
//        $charset = strlen($charset)? "charset=$charset" : "";
//        putenv("LC_ALL=$locale");
//        setlocale(LC_ALL, $locale);
//        $dir = dirname($_SERVER['SCRIPT_FILENAME']);
//        bindtextdomain($locale, "$dir/locale");
//        textdomain($locale);
//        if (strlen($charset))
//            header("Content-Type: text/html; $charset");
//    }
//}

function pacsone_gettext($text)
{
    return function_exists("gettext")? _($text) : $text;
}

?>
