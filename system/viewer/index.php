<?php
include_once($_SERVER["DOCUMENT_ROOT"]."/system/import.php");
import("system.core.database.MySQLDatabase");

session_start();
$studyList = $_REQUEST['entry'];
if($studyList != "")InsertFlashDicomViewer($studyList);
include_once($_SERVER["DOCUMENT_ROOT"]."/system/import.php");
include($_SERVER["DOCUMENT_ROOT"]."/system/config.php");
import('system.models.User');
import('system.logger');
$user = unserialize($_SESSION["AUTH_USER"]);

if(!is_array($studyList))
	$studyList = array($studyList);

foreach ($studyList as $studyID)
{
	$_logEvent = array();
	$_logEvent['event_type'] = 'User updated';
	$_logEvent['event_table'] = 'study';
	$_logEvent['event_table_id'] = $studyID;
	$_logEvent['additional_text'] = 'Study '.$studyID.' viewed';
	logger::log($_logEvent);
}

function InsertFlashDicomViewer($OnlyStudyList)
{
	if (isset($_REQUEST['debug']))
		$filename = 'DicomViewer_debug.swf';
	else
		$filename = 'DicomViewer.swf';
	
	
	include($_SERVER["DOCUMENT_ROOT"]."/system/config.php");
	$StudyList = $OnlyStudyList.'&ServerPath='.$DW_ServerSciptPath.
	'&StudyPath='.$DW_StudyPath.'&DicomViewerPath='.$DW_DicomViewerPath;
	
	echo"
	<script src=\"../js/AC_OETags.js\" language=\"javascript\"></script>
	
	<style>
	body { margin: 0px; overflow:hidden }
	</style>
	<script language=\"JavaScript\" type=\"text/javascript\">
	<!--
	// -----------------------------------------------------------------------------
	// Globals
	// Major version of Flash required
	var requiredMajorVersion = 9;
	// Minor version of Flash required
	var requiredMinorVersion = 0;
	// Minor version of Flash required
	var requiredRevision = 124;
	// -----------------------------------------------------------------------------
	// -->
	</script>
	<script language=\"JavaScript\" type=\"text/javascript\">
	<!--
	// Version check for the Flash Player that has the ability to start Player Product Install (6.0r65)
	var hasProductInstall = DetectFlashVer(6, 0, 65);
	
	// Version check based upon the values defined in globals
	var hasRequestedVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);
	
	if ( hasProductInstall && !hasRequestedVersion ) {
		// DO NOT MODIFY THE FOLLOWING FOUR LINES
		// Location visited after installation is complete if installation is required
		var MMPlayerType = (isIE == true) ? \"ActiveX\" : \"PlugIn\";
		var MMredirectURL = window.location;
	    document.title = document.title.slice(0, 47) + \" - Flash Player Installation\";
	    var MMdoctitle = document.title;
	
		AC_FL_RunContent(
			\"src\", \"playerProductInstall\",
			\"FlashVars\", \"MMredirectURL=\"+MMredirectURL+'&MMplayerType='+MMPlayerType+'&MMdoctitle='+MMdoctitle+'&StudyList=$StudyList'+\"\",
			\"width\", \"100%\",
			\"height\", \"100%\",
			\"align\", \"middle\",
			\"id\", \"DicomViewer\",
			\"quality\", \"high\",
			\"bgcolor\", \"#869ca7\",
			\"name\", \"DicomViewer\",
			\"allowScriptAccess\",\"sameDomain\",
			\"type\", \"application/x-shockwave-flash\",
			\"pluginspage\", \"http://www.adobe.com/go/getflashplayer\"
		);
	} else if (hasRequestedVersion) {
		// if we've detected an acceptable version
		// embed the Flash Content SWF when all tests are passed
		AC_FL_RunContent(
				\"src\", \"DicomViewer\",
				\"flashVars\", \"StudyList=$StudyList\",
				\"width\", \"100%\",
				\"height\", \"100%\",
				\"align\", \"middle\",
				\"id\", \"DicomViewer\",
				\"quality\", \"high\",
				\"bgcolor\", \"#869ca7\",
				\"name\", \"DicomViewer\",
				\"allowScriptAccess\",\"sameDomain\",
				\"type\", \"application/x-shockwave-flash\",
				\"pluginspage\", \"http://www.adobe.com/go/getflashplayer\"
		);
	  } else {  // flash is too old or we can't detect the plugin
	    var alternateContent = 'Alternate HTML content should be placed here. '
	  	+ 'This content requires the Adobe Flash Player. '
	   	+ '<a href=http://www.adobe.com/go/getflash/>Get Flash</a>';
	    document.write(alternateContent);  // insert non-flash content
	  }
	// -->
	</script>
	<noscript>
	  	<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\"
				id=\"DicomViewer\" width=\"100%\" height=\"100%\"
				codebase=\"http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab\">
				<param name=\"movie\" value=\"$filename\" />
				<param name=\"quality\" value=\"high\" />
				<param name=\"bgcolor\" value=\"#869ca7\" />
				<param name=\"flashVars\" value=\"StudyList=$StudyList\" />
				<param name=\"allowScriptAccess\" value=\"sameDomain\" />
				<embed src=\"$filename\" quality=\"high\" bgcolor=\"#869ca7\"
					width=\"100%\" height=\"100%\" name=\"DicomViewer\" align=\"middle\"
					play=\"true\"
					loop=\"false\"
					StudyList=\"$StudyList\",
					quality=\"high\"
					allowScriptAccess=\"sameDomain\"
					type=\"application/x-shockwave-flash\"
					pluginspage=\"http://www.adobe.com/go/getflashplayer\">
				</embed>
		</object>
	</noscript>
	";
}
?>