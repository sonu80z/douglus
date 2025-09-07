<?php
import("system.core.orm.Model");
/**
 * @author Jesse Chrestler
 * @name GroupType 
 * 
 * Modified: 07-06-2009
 * 
 * <metadata>
 * tableAlias:rprs_group_types
 * </metadata>
 */
class GroupType extends Model
{
	/**
	 * <metadata>
	 * primaryKey:true
	 * </metadata>
	 */
	public $id;
	public $name;
}
?>