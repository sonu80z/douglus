<?php
import("system.core.orm.Model");
/**
 * @author Jesse Chrestler
 * @name UserGroup
 * 
 * Modified: 07-06-2009
 * 
 * <metadata>
 * tableAlias:rprs_user_groups
 * 
 * </metadata>
 */
class UserGroup extends Model
{
	/**
	 * <metadata>
	 * primaryKey:true
	 * foreignKey: User.id
	 * </metadata>
	 */
	public $userid;
	/**
	 * <metadata>
	 * primaryKey:true
	 * foreignKey: Group.id
	 * </metadata>
	 */
	public $groupid;
}
?>