<?php
import("system.core.orm.Model");
/**
 * @author Jesse Chrestler
 * @name User 
 * 
 * Modified: 07-06-2009
 * 
 * <metadata>
 * tableAlias:rprs_users
 * relatedTo:UserGroup
 * </metadata>
 */
class User extends Model{
	/**
	 * <metadata>
	 * primaryKey:true
	 * </metadata>
	 */
	public $id;
	public $firstname;
	public $middlename;
	public $lastname;
	public $username;
	public $password;
	public $passwordexpired;
	public $canmailpdf;
	public $canbatchprintpdfs;
	public $canmarkasreviewed;
	public $selfonly;
	public $admin;
	public $canburncd;
	public $canmarkcritical;
	public $canattachorder;
	public $canaddnote;
	public $staffrole;
	
	function __construct($attributes = array())
	{
		if(isset($attributes["password"])){
			$attributes["password"] = $this->EncryptField($attributes["password"]);
		}
		parent::__construct($attributes);
	}
	function SetPassword($password){
		$this->password = $this->EncryptField($password);
	}
}
?>