<?php
/**
 * This function is to properly escape a given string
 * so that it will be interpreted by javascript with out problems
 */
class Format{
	public static function Javascript($string){
		if(is_null($string)){
			return '';
		}
		return strtr($string, array('\\'=>'\\\\',"'"=>"\\'",'"'=>'\\"',"\r"=>'\\r',"\n"=>'\\n','</'=>'<\/'));
	}
	public static function MySQL($string){
		return strtr($string, array("'"=>"''"));
	}
}
?>