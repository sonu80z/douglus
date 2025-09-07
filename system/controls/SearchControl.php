<?php
import("system.models.Group");
import("system.models.GroupUser");
import("system.controls.control");
import("system.core.database.MySQLDatabase");

class SearchControl extends Control
{
	public function ViewSearchTypes($args){
		$params = $this->GetQueryParams($args, array("sort"=>"type ASC"));
		return $this->controller->FindAll("SearchType", $params)->toJSON();
	}
}
?>