<?php

/**
 * @author Jesse Chrestler
 * @name Metadata 
 * 
 * Modified: 07-06-2009
 * 
 * Usage: to create metadata for a class,property or method.
 */
import("system.core.data.containers.DynamicObject");
class Metadata extends DynamicObject{
	public function setDefaults($attributes){
		foreach($attributes as $key => $value)
			if(!is_array($this->attributes))$this->attributes = array();
			if(!array_key_exists($key, $this->attributes))
				$this->attributes[$key] = $value;
	}
	private static function GetMetaData($metaProperty){
		$comment = $metaProperty->getDocComment();
		if(preg_match("#<metadata>.+</metadata>#is", $comment)){
			//first lets strip out metadata
			$comment = preg_replace("#.<metadata>.</metadata>.#is", "\\1", $comment);
			//now lets strip out all extra stuff in front of meta data.
			$comment = preg_replace("#([\t ]*\*+ {1})#is", "", $comment);
		}else $comment = "";
		return MetaData::ParseMetaData(explode("\n", $comment));
	}
	private static function ParseMetaData($arr){
		$regex = "^ ";
		$len = count($arr);
		$ret = array();
		for($i = 0;$i < $len;$i++){
			$map = explode(":", $arr[$i]);
			if(sizeof($map) > 1 && trim($map[1]) == ""){
				$start = $i+1;
				for($i+=1;$i < $len;$i++){
					if(!preg_match($regex, $arr[$i])){
						$sublen = $i - $start;
						$i--;
						$ret[trim($map[0])] = MetaData::ParseMetaData(array_slice($arr, $start, $sublen));
						break;
					}else{
						$arr[$i] = preg_replace($regex, "", $arr[$i]);
						if($i == $len-1)
							$ret[trim($map[0])] = MetaData::ParseMetaData(array_slice($arr, $start, $len));
						}
					}	
			}else if($map[0] != "" && sizeof($map) > 1){
				$ret[trim($map[0])] = trim(Metadata::GetRealValue($map[1]));
			}
		}
		return $ret;
	}
	private static function GetRealValue($val){
		if(preg_match("/^true$/", $val)) $val = true;
		if(preg_match("/^false$/", $val)) $val = false;
		if(preg_match("/^\d+$/", $val))$val = (int)$val;
		return $val;
	}
	public static function Attributes($class){
		return new MetaData(MetaData::GetMetaData(new ReflectionClass($class)));
	}
	public static function Method($class, $method){
		return new MetaData(MetaData::GetMetaData(new ReflectionMethod($class,$method)));
	}
	public static function Property($class, $prop){
		return new MetaData(MetaData::GetMetaData(new ReflectionProperty($class,$prop)));
	}
}
?>

