<?php
import("system.core.orm.Model");
/**
 * @author Jesse Chrestler
 * @name GroupType 
 * 
 * Modified: 07-06-2009
 * 
 * <metadata>
 * tableAlias:rprs_search_types
 * </metadata>
 */
class SearchType extends Model
{
	/**
	 * <metadata>
	 * primaryKey:true
	 * </metadata>
	 */
	public $id;
	public $type;
	public $search_column;
}
?>