<?php
import("system.core.orm.Model");
/**
 * @author Jesse Chrestler
 * @name GroupType 
 * 
 * Modified: 07-06-2009
 * 
 * <metadata>
 * tableAlias:institution_mail_addresses
 * </metadata>
 */
class MailType extends Model
{
	/**
	 * <metadata>
	 * primaryKey:true
	 * </metadata>
	 */
	public $id;
	public $mail;
	public $institution;
}
?>