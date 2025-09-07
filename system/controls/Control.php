<?php


import("system.core.orm.DataController");
import("system.utilities.JSON");
import("system.core.database.QueryParams");
class Control
{
	protected $controller;
	protected $return;
	public function __construct($database = null){
		$this->controller = new DataController($database);
		$this->return["success"] = "false";
	}
	protected function GetQueryParams(& $args, $defaults = array()){
		$params = new QueryParams($defaults);
		if(isset($args["start"]) && isset($args["limit"])){
			$params->start = $args["start"];
			$params->limit = $args["limit"];
			unset($args["start"]);
			unset($args["limit"]);
		}
		if(isset($args["sort"]) && isset($args["dir"])){
			$params->sort = $args["sort"];
			$params->dir = $args["dir"];
			unset($args["sort"]);
			unset($args["dir"]);
		}
		return $params;
	}
	protected function GetJSON($result){
		if(is_object($result)){
			if((is_numeric($result->rowsAffected) && $result->rowsAffected > 0) || $result->error == ""){
				$this->return["success"]= "true";
			}else {
				$this->return["msg"] = $result->error;
			}
		}
		return JSON::Encode($this->return);
	}
}

?>