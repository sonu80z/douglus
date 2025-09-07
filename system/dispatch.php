<?php
/**
 * @author Jesse Chrestler
 * @name dispatch.php
 * 
 * Modified: 07-08-2009
 * 
 * Usage:used to dispatch calls (via ajax) between the page and the controls
 * 
 */
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	include_once($_SERVER["DOCUMENT_ROOT"]."/system/import.php");
	import("system.controls.*");
	$var = array();
	// wrap up all the variables in the $_GET object
	foreach($_GET as $key => $value) $var[$key] = $value;
	// wrap up all the variables in the $_POST object
	foreach($_POST as $key => $value) $var[$key] = $value;
	if(isset($var['control']) && $var["control"] != "" && $var["method"] != "")
	{
		//die(print_r($var,true));
		//lets include the control that we need to call
		$class = new $var["control"];
		$args = array();
		//all other variables will be arguments that are to be passed into control
		foreach ($var as $key=>$value)
			if(!preg_match("#(control|method|_dc)#is", $key))
			{
				$args[$key] = $value;
			}
		if (empty($_SESSION))
			@session_start();
                if(empty($_SESSION["AUTH_USER"]))
                {
                    if ($var["method"] != 'getImage' && $var['method'] != 'processSRFiles' && $var['method'] != 'logBurnCDActivity')
                    {
                        echo 'You should relogin first';
                        exit();
                    }
                }
		print $class->{$var["method"]}($args);
	}
