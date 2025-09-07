<?php
import("system.core.orm.Model");
/**
 * @author Jesse Chrestler
 * @name Group
 * 
 * Modified: 07-06-2009
 * 
 * <metadata>
 * tableAlias:rprs_groups
 * relatedTo:UserGroup
 * </metadata>
 */
class Group extends Model
{
	/**
	 * <metadata>
	 * primaryKey:true
	 * </metadata>
	 */
	public $id;
	/**
	 * <metadata>
	 * foreignKey: GroupType.id
	 * </metadata>
	 */
	public $grouptypeid;
	public $name;
	public $description;
	public $filterdata;
}

?>