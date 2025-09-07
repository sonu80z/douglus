<?php
import("system.core.orm.Model");
/**
 * @author Jesse Chrestler
 * @name Patient
 * 
 * Modified: 07-14-2009
 * 
 */
class Patient extends Model
{
	/**
	 * <metadata>
	 * primaryKey:true
	 * </metadata>
	 */
	public $origid;	public $lastname;	public $firstname;	public $middlename;	public $prefix;	public $suffix;	public $birthdate;	public $birthtime;	public $sex;	public $otherid;	public $othername;	public $ethnicgroup;	public $institution;	public $comments;	public $age;	public $height;	public $weight;	public $occupation;	public $history;	public $private;	public $lastaccess;	public $patientmatchworklist;
}

?>