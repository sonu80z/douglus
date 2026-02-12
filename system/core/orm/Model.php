<?php
/**
 * @author Jesse Chrestler
 * @name Model 
 * 
 * Modified: 07-06-2009
 * 
 * Usage: base object for all data value objects, used to pass to the database for add, updates, and deletes.
 * 
 * see below for metadata options
 * <metadata>
 * tableName: nameoftable
 * </metadata>
 */
import("system.core.reflection.Metadata");
import("system.utilities.*");

class Model
{
	//table name alias
	function __construct($properties = array()){
		$this->SetProperties($properties);
	}
	public function SetProperties($properties){
		if(is_array($properties) && sizeof($properties) > 0){
			foreach($properties as $key => $value){
        $key = strtolower($key);
				if(property_exists($this, $key))
					$this->{$key} = $value;
			}	
		}
	}
	public function GetTableName(){
		$metadata = $this->GetTableSchema();
		return strtolower(($metadata->tableAlias != "") ? $metadata->tableAlias : get_class($this));
	}
	public function ClearSchemaSession(){
		unset($_SESSION[get_class($this)]);
	}
	public function GetColumnSchema(){
		if(!isset($_SESSION[get_class($this)]["columnSchema"])){
			$properties = get_object_vars($this);
			$columnSchema = array();
			foreach($properties as $key => $value){
				$metadata = Metadata::Property($this, $key);
				$metadata->setDefaults(array('primaryKey'=>false));
				$columnSchema[$key] = $metadata;
			}
			$_SESSION[get_class($this)]["columnSchema"] = serialize($columnSchema);
		}
                $ret = $_SESSION[get_class($this)]["columnSchema"];
                // Arbuzov: very weird - next row prints \n to the output, IDK;
                $ret = @unserialize($ret);
		return $ret;
	}
	
	public function GetTableSchema(){
		if(!isset($_SESSION[get_class($this)]["tableSchema"]))
			$_SESSION[get_class($this)]["tableSchema"] = serialize(Metadata::Attributes(get_class($this)));
		return unserialize($_SESSION[get_class($this)]["tableSchema"]);
	}
	public function __toString(){
		$returnvalue = array();
		foreach($this->GetColumnSchema() as $key => $metadata)
			array_push($returnvalue, "'".$key."'='".Format::Javascript($this->{$key})."'");
		return "{".join(",", $returnvalue)."}";
	}
	protected function EncryptField($field){
		return Encrypt::Data($field);
	}
}
?>