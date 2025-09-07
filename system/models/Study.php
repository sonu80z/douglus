<?php
import("system.core.orm.Model");
/**
 * @author Jesse Chrestler
 * @name Study
 * 
 * Modified: 07-14-2009
 * 
 */
class Study extends Model
{
	/**
	 * <metadata>
	 * primaryKey:true
	 * </metadata>
	 */
	public $uuid;
	public $id;
	public $patientid;
	public $studydate;
	public $studytime;
	public $accessionnum;
	public $modalities;
	public $referringphysician;
	public $description;
	public $readingphysician;
	public $admittingdiagnoses;
	public $interpretationauthor;
	public $private;
	public $received;
	public $updated;
	public $sourceae;
	public $reviewed;
	public $compressed;
	public $matched;
	public $studymatchworklist;
	public $reviewed_user_id;
	public $reviewed_date;
	public $is_critical;
	public $critical_date;
	public $mailed_date;
	public $has_attached_orders;
    public $has_tech_notes;
}

?>