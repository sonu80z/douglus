<?php
class QueryParams
{

	public $start = "";
	public $limit = "";
	public $sort = "";
	public $dir = "";
	public $conditions = "";
	function __construct($properties = array()){
		if(is_array($properties) && sizeof($properties) > 0){
			foreach($properties as $key => $value){
				if(array_key_exists($key, $this))
					$this->{$key} = $value;
			}	
		}
	}
}