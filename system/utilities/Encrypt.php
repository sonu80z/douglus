<?php
class Encrypt
{
	public static function Data($data){
		return sha1($data);
	}
}
?>