<?php
import("system.core.orm.Model");
/**
 * @author Jesse Chrestler
 * @name Image
 * 
 * Modified: 07-14-2009
 * 
 */
class Image extends Model
{
	/**
	 * <metadata>
	 * primaryKey:true
	 * </metadata>
	 */
	public $uuid;	public $sopclass;	public $seriesuid;	public $xfersyntax;	public $date;	public $time;	public $instance;	public $overlay;	public $curve;	public $lut;	public $samplesperpixel;	public $numrows;	public $numcolumns;	public $bitsallocated;	public $bitsstored;	public $pixelrepresentation;	public $photometric;	public $path;	public $completion;	public $description;	public $verification;	public $contentdate;	public $contenttime;	public $observationdatetime;	public $verificationdatetime;	public $doseproduct;	public $tagged;	public $numframes;
}

?>