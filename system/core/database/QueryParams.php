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
				if(property_exists($this,$key ))
					$this->{$key} = $value;
			}	
		}
	}
}