<?php
/**
 * @author Jesse Chrestler
 * @name DataController 
 * 
 * Modified: 07-06-2009
 * 
 * Usage: used to talk directly to the database, and translate models into sql code.
 * 
 */
import("system.core.database.*");
import("system.models.*");
class DataController {
	private $db;
	function __construct($database = null){
		$this->db = MySQLDatabase::GetInstance($database);
	}
	public function Add($model){
		$cols = $this->GetColumns($model, true, true);
		$sql = "INSERT INTO " . $model->GetTableName() . " (" . join(",",array_keys($cols) ) . ") ".
		"VALUES (" . join( ",",array_values($cols)). ")";
		return $this->db->ExecuteNonQuery($sql);
	}
	public function Update($model){
		$keys = $this->GetColumnValuePair($model, true, false);
//		die('<pre>'.print_r($keys, 1).'</pre>');
		if(count($keys) > 0){
			$cols = $this->GetColumnValuePair($model);
			$sql = "UPDATE " . $model->GetTableName() . 
			" SET " . join(" , ",$cols).
			" WHERE " . join(" AND ",$keys);
//		echo ('<pre>'.print_r($sql, 1).'</pre>');
		}else throw new Exception("Failed to update no primary key was set");
		return $this->db->ExecuteNonQuery($sql);
	}
	public function Delete($model){
		$keys = $this->GetColumnValuePair($model, true, true);
		if(count($keys) > 0)
			$sql = "DELETE FROM " . $model->GetTableName() . " WHERE ". join(" AND ",$keys);
		else 
			throw new Exception("Failed to delete need a condition for deleting.");
		return $this->db->ExecuteNonQuery($sql);
	}
	public function Find($model, $params = null,$debug=false){
                if($params == null) $params = new QueryParams();
		$keys = $this->GetColumnValuePair($model, true, true);
		$tablename = $model->GetTableName();
		$sqlCount = "";
		$sql = "SELECT * FROM " . $tablename. " WHERE 1 = 1 " ;
		if(count($keys) > 0)
        {    
            $sql .= " AND " . join( " AND ", $keys);
        }
        if($params->conditions != "")
        {
            $sql .= " AND ".$params->conditions;
        }
		if($params->sort != ""){
			$sql .= " ORDER BY ".$tablename."." . $params->sort . " " . $params->dir;
		}
		if($params->limit != "") {
			$sqlCount = "SELECT count(*) rowcount FROM ". preg_replace("#(SELECT.+FROM|ORDER BY.+)#is", "", $sql);
			$sql .= " LIMIT ". $params->start. ", " . $params->limit;
		}
//	die('<pre>'.print_r($sql, 1).'</pre>');
	if($debug){
		echo $sql;
		exit;
	}
		$reader = $this->db->ExecuteReader($sql);
		if($params->limit != "") {
			$row = $this->db->ExecuteReader($sqlCount)->GetNextAssoc();
			$reader->recordCount = $row["rowcount"];
		}
		return $reader;
	}
	public function FindAll($model, $params = null){
		if($params == null) $params = new QueryParams();
		if(is_string($model)) $model = new $model;
		return $this->Find($model, $params);
	}
	private function GetValue($value){
		if(!is_numeric($value) && !preg_match("#(null|true|false)#is",$value)){
			//escaping the value incase there is a single quote.
			$value = str_replace("'", "''", $value);
			$value = "'$value'";
		}
		return $value;
	}
	private function GetColumnValuePair($model, $pk = false, $npk = true){
                $cols = $this->GetColumns($model,$pk, $npk);
		$schema = $model->GetColumnSchema();
		$pair = array();
		foreach ($cols as $col=>$val){
			$key = preg_replace("#.+\.#is", "", $col);
			if(!$schema[$key]->primaryKey || $val != "null"){
				$pair[$key] = $col." = ".$val;
			}
		}
		return $pair; 
	}
	//$pk = primarykey && $npk = non-primary key
	private function GetColumns($model, $pk = false, $npk = true){
		$colschema = $model->GetColumnSchema();
		$cols = array();
		foreach($colschema as $key => $metadata){
			if((!$metadata->primaryKey && $npk) || ($pk && $metadata->primaryKey)){
				if(isset($model->{$key}))
					$cols[$model->GetTableName().".".$key] = $this->GetValue($model->{$key});
			}
		}
		return $cols;
	}
}