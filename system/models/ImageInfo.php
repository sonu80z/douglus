<?php
import("system.core.orm.Model");
/**
 * @author Jesse Chrestler
 * @name Image
 * 
 * Modified: 07-14-2009
 * 
 */
class ImageInfo extends Model
{
	/**
	 * <metadata>
	 * primaryKey:true
	 * </metadata>
	 */
	public $seriesuid;
	public $uuid;
	public $instance;
	public $path;
}
?>