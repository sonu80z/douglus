<?php
import("system.core.orm.Model");
/**
 * @author Kirill Arbuzov
 * @name Notes
 * 
 * Modified: 07-14-2009
 * 
 */
class Notes extends Model
{
	/**
	 * <metadata>
	 * primaryKey:true
	 * </metadata>
	 */
	public $studyid;
	public $username;
	public $notedate;
	public $text;
}

?>