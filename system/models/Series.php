<?php
import("system.core.orm.Model");
/**
 * @author Jesse Chrestler
 * @name Series
 * 
 * Modified: 07-14-2009
 * 
 */
class Series extends Model
{
	/**
	 * <metadata>
	 * primaryKey:true
	 * </metadata>
	 */
	public $uuid;	public $studyuid;	public $description;	public $date;	public $time;	public $modality;	public $bodypart;	public $number;	public $instances;	public $protocolname;
}

?>