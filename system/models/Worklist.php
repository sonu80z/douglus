<?php
import("system.core.orm.Model");
/**
 * @author Jesse Chrestler
 * @name Worklist
 * 
 * Modified: 07-14-2009
 * 
 */
class Worklist extends Model
{
	/**
	 * <metadata>
	 * primaryKey:true
	 * </metadata>
	 */
	public $studyuid;	public $id;	public $state;	public $classuid;	public $patientname;	public $patientid;	public $birthdate;	public $sex;	public $accessionnum;	public $requestingphysician;	public $referringphysician;	public $createdate;	public $createtime;	public $creatoruid;	public $scheduledstartdate;	public $scheduledstarttime;	public $location;	public $aetitle;	public $arrivedate;	public $arrivetime;	public $startdate;	public $starttime;	public $completedate;	public $completetime;	public $verifydate;	public $verifytime;	public $readdate;	public $readtime;	public $status;	public $received;	public $pregnancystat;	public $lastmenstrual;
}

?>