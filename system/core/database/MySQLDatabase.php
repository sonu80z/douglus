<?php
/**
 * @author Jesse Chrestler
 * @name MySQLDatabase 
 * 
 * Modified: 07-06-2009
 * 
 * Usage: used to connect to the database, core library
 * 
 */
import("system.core.database.interface.*");
class MySQLDatabase implements IDatabase {
	private $connection;
	private static $instance;
	private static $host;
	private static $user;
	private static $pass;
	private static $database;
	private function __construct($database = null){
		$this->connection = mysqli_connect(MySQLDatabase::$host, MySQLDatabase::$user, MySQLDatabase::$pass);
		$this->SetDatabase($database);
		$this->PrintErrorMessage();
	}
	public static function SetConnectionDefault($host, $user, $pass, $database){
		MySQLDatabase::$host = $host;
		MySQLDatabase::$user = $user;
		MySQLDatabase::$pass = $pass;
		MySQLDatabase::$database = $database;
	}
	public static function GetInstance($database = null){
		if(MySQLDatabase::$instance == null){
			MySQLDatabase::$instance = new MySQLDatabase($database);
		}
		return MySQLDatabase::$instance;
	}
	public function getConnection(){
		return $this->connection;
	}
	public function SetDatabase($database){
		if(!isset($database) || $database == null) $database = MySQLDatabase::$database;
		mysqli_select_db($this->connection, $database);
	}
	public function disconnect(){
		mysqli_close($this->connection);
	}
	public function ExecuteReader($sql){
		//echo $sql;
		return new MySQLRecordReader($this->connection, mysqli_query($this->connection, $sql));
	}
	public function ExecuteNonQuery($sql){
		//echo $sql;
		$sres = mysqli_query($this->connection, $sql);
                $error = '';
                if (!$sres){
                    $error = mysqli_error($this->connection);
                }
                $rowsAffected = 0;
                
                if($error === ''){
                    $rowsAffected = mysqli_affected_rows($this->connection);
		}
		return new MySQLResult($rowsAffected, mysqli_insert_id($this->connection), $error);
	}
	private function PrintErrorMessage(){
		$error = mysqli_error($this->connection);
		if($error != "")
			echo "<br /><span style='color:red'>[mysqli_error]</span>->\n" . $error ."\n<br />";
	}
}
//sets the default configruation
include($_SERVER["DOCUMENT_ROOT"]."/system/config.php");
MySQLDatabase::SetConnectionDefault($DB_HOST, $DB_USER, $DB_PASS, $DB_DATABASE);
/**
 * @author Jesse Chrestler
 * @name MySQLRecordReader 
 * 
 * Modified: 07-06-2009
 * 
 * Usage: used to read the resultset passed back from SQLDatabase
 * 
 */
class MySQLRecordReader implements IRecordReader{
	private $resultset;
	public $recordCount;
	public $error;
        public $connection;
	function __construct($connection, $resultset){
		$this->error = '';
                $this->connection = $connection;
		$this->resultset = $resultset;
		if(isset($this->resultset) and $this->resultset != ""){
			$this->recordCount = mysqli_num_rows($this->resultset);
		}else{
			$this->recordCount = 0;
		}
	}
	public function getResultSet(){
		return $this->resultset;
	}
	public function GetNextArray(){
		return ($this->recordCount > 0) ? mysqli_fetch_array($this->resultset) : array();
	}
	public function GetNextAssoc(){
		return ($this->recordCount > 0 && $this->resultset != null) ? mysqli_fetch_assoc($this->resultset) : array();
	}
	public function toObject($type){
		$objects = array();
		while ($row = $this->GetNextAssoc()){
			array_push($objects, new $type($row));
		}
		return $objects;
	}
	public function toJSON($showRecordCount = true){
		$json = array();
		while ($row = $this->GetNextAssoc()){
			$jsonRow = array();
			foreach($row as $col=>$val){
				array_push($jsonRow, strtolower($col).":'".mysqli_real_escape_string($this->connection, $val)."'");	
			}
			array_push($json, "{".join($jsonRow, ",")."}");	
		}
		$returnvalue = "[".join($json, ",")."]";
		if($showRecordCount) $returnvalue  = "{recordcount:".$this->recordCount.", data:".$returnvalue."}";
		return $returnvalue;
	}
}
class MySQLResult {
	public $rowsAffected;
	public $lastId;
	public $error;
	function __construct($rowsAffected, $lastId, $error){
		$this->rowsAffected = $rowsAffected;
		$this->lastId = $lastId;
		$this->error = $error;
	}
}