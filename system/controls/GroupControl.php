<?php
import("system.models.Group");
import("system.models.GroupUser");
import("system.controls.control");
import("system.core.database.MySQLDatabase");
class GroupControl extends Control
{
	public function View($args){
		$params = $this->GetQueryParams($args, array("start"=>"0", "limit"=>"12", "dir"=>"ASC", "sort"=>"name"));
		$db = MySQLDatabase::GetInstance();
            if (isset($args['sort']))
            {
                $arr = json_decode(stripslashes($args['sort']));
                $params->sort = $arr[0]->property;
                $params->dir = $arr[0]->direction;
            }
		$WHERE = '';
		if (isset($args['search']) && (strlen($args['search'])>0))
			$WHERE = " where g.name like '%$args[search]%' or gt.name like '%$args[search]%' ";
		$sql = "select g.*, gt.name as type from rprs_groups g join rprs_group_types gt on gt.id = g.grouptypeid ". 
		$WHERE."order by ".
		$params->sort . " ". $params->dir . " LIMIT " . $params->start . "," . $params->limit;
//		die($sql);
		$rs = $db->ExecuteReader($sql);
		$count = $db->ExecuteReader("select count(*) from rprs_groups g join rprs_group_types gt on gt.id = g.grouptypeid $WHERE")->GetNextArray();
		$rs->recordCount = $count[0];
		return $rs->toJSON();
	}
	public function ViewGroupTypes($args){
		$params = $this->GetQueryParams($args, array("sort"=>"name ASC"));
		return $this->controller->FindAll("GroupType", $params)->toJSON();
	}
	public function ViewGroupUsers($args){
		$params = $this->GetQueryParams($args);
		$db = MySQLDatabase::GetInstance();
		return $db->ExecuteReader("select u.id, u.username from rprs_users u join rprs_group_users gu on gu.userid = u.id where gu.groupid = ".$args["groupid"]. " order by u.username")->toJSON();
	}
	public function ViewGroupTypeCriteria($args){
		if(isset($args["grouptypeid"])){
			$criteria = "''";
			$grouptype = $this->controller->Find(new GroupType(array("id"=>$args["grouptypeid"])))->toObject("GroupType");
//		die('<pre>'.print_r($args, 1).'</pre>');
			if ($grouptype && isset($grouptype[0]))
				$grouptype = $grouptype[0];
			$db = MySQLDatabase::GetInstance();
			if(isset($args["groupid"]))
			{
				$group = $this->controller->Find(new Group(array("id"=>$args["groupid"])))->toObject("Group");
				if ($group && isset($group[0]))
				{
					$group = $group[0];
					$criteria .= ",'".join("','",explode("|",$group->filterdata))."'";
				}
			}
			$query = "";
			$search = "";
			$grouptype_name = '';
			if ($grouptype && isset($grouptype->name))
				$grouptype_name = $grouptype->name;
				
			if($grouptype_name == "Referring Physician"){
				if(isset($args["search"]))$search = " and referringphysician like '%" . $args["search"] . "%' ";
				$query = "select distinct referringphysician data from study where referringphysician is not null and referringphysician not in (".
					$criteria.") ".$search." order by referringphysician";
			
			}else if($grouptype_name == "Institution"){
				if(isset($args["search"]))$search = " and institution like '%" . $args["search"] . "%' ";
				$query = "select distinct institution data from patient where institution is not null and institution not in (".
					$criteria.") ".$search." order by institution";
			}else if ($grouptype_name == "Consult"){
				if(isset($args["search"]))$search = " and origid like '%" . $args["search"] . "%' ";
				$query = "select origid data from patient where origid is not null and origid not in (".
					$criteria.") ".$search." order by origid";
			}
			return $db->ExecuteReader($query)->toJSON();
		}else{
			return "{recordcount:0, data:[]}";
		}
	}
	public function Delete($args){
    if(isset($args["id"])){
      $this->GetJSON($this->controller->Delete(new GroupUser(array("groupid"=>$args["id"]))));
    }
		return $this->GetJSON($this->controller->Delete(new Group($args)));
	}
	public function Add($args){
		$userlist = explode("|", $args["userid"]);
                $args['id'] = null;
		$result = $this->controller->Add(new Group($args));
		foreach($userlist as $index=>$userid){
			$this->controller->Add(new GroupUser(array("userid"=>$userid, "groupid"=>$result->lastId)));
		}
		return $this->GetJSON($result);
	}
	public function Update($args){
		$userlist = explode("|", $args["userid"]);
		$group = new Group($args);
		$result = $this->controller->Update($group);
		$this->controller->Delete(new GroupUser(array("groupid"=>$group->id)));
		foreach($userlist as $index=>$userid){
			$this->controller->Add(new GroupUser(array("userid"=>$userid, "groupid"=>$group->id)));
		}
		return $this->GetJSON($result);
	}
}

?>