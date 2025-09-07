<?php

// Set the appropriate MIME type for the JNLP descriptor
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header('Content-Type: application/x-java-jnlp-file');
header("Content-Disposition: inline; filename=launchRemotEye.jnlp");
print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

// Dynamically get the codebase for the JNLP
$thisScriptDirURL = "";
if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == "on"))
{
	$thisScriptDirURL = "https://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
}
else
{
	$thisScriptDirURL = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
}
$parentDirURL = substr($thisScriptDirURL, 0, strrpos($thisScriptDirURL, '/'));

print "<jnlp spec=\"1.0+\" codebase=\"" . $parentDirURL . "\">\n";
?>

<information>
	<title>RemotEye Viewer</title>
	<vendor>NeoLogica s.r.l.</vendor>
	<description>RemotEye Web DICOM Viewer</description>
	<icon kind="default" href="Images/RELogo_48x48.png" width="48" height="48"/>
	<icon kind="splash" href="Images/SplashScreen.png"/>
	<offline-allowed/>
</information>

<update check="timeout" policy="prompt-update"/>

<security>
	<all-permissions/>
</security>

<!-- Global resources for all OS's //-->
<resources>
	<property name="jnlp.packEnabled" value="true"/>
	<j2se version="1.7+"
		href="http://java.sun.com/products/autodl/j2se"
		initial-heap-size="64m"
		max-heap-size="640m"
		java-vm-args="-Dsun.java2d.noddraw=true"/>
	<jar href="RemotEyeViewer.jar" main='true'/>
</resources>


<resources os="Windows" arch="x86">
	<nativelib href="WSResources/RemotEyeNativeLibs-win32.jar" download="eager"/>
</resources>

<resources os="Windows" arch="amd64">	
	<nativelib href="WSResources/RemotEyeNativeLibs-win64.jar" download="eager"/>
</resources>

<resources os="Linux" arch="i386">
	<nativelib href="WSResources/RemotEyeNativeLibs-linux32.jar" download="eager"/>
</resources>

<resources os="Linux" arch="amd64">
	<nativelib href="WSResources/RemotEyeNativeLibs-linux64.jar" download="eager"/>
</resources>

<resources os="Mac OS X">
	<nativelib href="WSResources/RemotEyeNativeLibs-macosx.jar" download="eager"/>
</resources>

<application-desc main-class="imageviewer.RemotEye">

	<argument>licenseManagerURL=PHP/licenseManager.php</argument>
	<argument>stringTableURL=StringTables/StringTable_EN-US.zip</argument>
	<argument>checkJREVersion=false</argument>
	<argument>checkMaxMemory=false</argument>
	<argument>singleInstanceBehavior=reuseExisting</argument>
	<argument>tileCacheSizeMegabytes=30%</argument>
	<argument>reinstallClientSide=false</argument>
	<argument>onlineHelpURL=Docs/UserManual_EN.pdf</argument>
	<argument>enableClientAuthentication=false</argument>
	<argument>enableRadiologistWorklist=true</argument>
	<argument>authenticationURL=PHP/authenticate.php</argument>
	<argument>integrationModel=query</argument>
	<argument>queryStudiesURL=PHP/queryStudies.php</argument>
	<argument>querySeriesURL=PHP/querySeries.php</argument>
	<argument>queryInstancesURL=PHP/queryInstances.php</argument>
	<argument>retrieveInstanceURL=PHP/retrieveInstance.php</argument>
	<argument>storeSOPInstanceURL=PHP/storeSOPInstance.php</argument>
	<argument>storeNonDICOMFileURL=PHP/storeNonDICOMFile.php</argument>
	<argument>querySRTemplatesURL=PHP/querySRTemplates.php</argument>
	<argument>storeSRTemplateURL=PHP/storeSRTemplate.php</argument>
	<argument>updateStudyInfoURL=PHP/updateStudyInfo.php</argument>
	<argument>retrieveStudyInfoURL=PHP/retrieveStudyInfo.php</argument>
	<argument>overlayTextColor=yellow</argument>
	<argument>overlayEnabledOnStartup=true</argument>
	<argument>overlayAllowUserSelection=true</argument>
	<argument>annotationsEnabledOnStartup=true</argument>
	<argument>showInNewFrame=true</argument>
	<argument>startAsJavaWS=true</argument>
	<argument>showFindFrameOnStartup=true</argument>
	<argument>showStudyDateTimeInTree=true</argument>
	<argument>showThumbnailsPanelOnStartup=true</argument>
	<argument>autoCloseFindFrame=true</argument>
	<argument>showMetaInfoTab=true</argument>
	<argument>showPatientInfoTab=true</argument>
	<argument>showStudyInfoTab=true</argument>
	<argument>showSeriesInfoTab=true</argument>
	<argument>showEquipmentInfoTab=true</argument>
	<argument>showImageInfoTab=true</argument>
	<argument>applyToWholeSeriesContrEnabledOnStartup=true</argument>
	<argument>thumbnailsSize=80</argument>
	<argument>defaultLoadURL=http://yourserver/DICOM/</argument>
	<argument>defaultLocalLoadPath=</argument>
	<argument>autoLoadOnStartup=firstSet</argument>
	<argument>seriesTiling=2x2</argument>
	<argument>imageTiling=1x1</argument>
	<argument>zoomInterpModeOnStartup=bilinear</argument>
	<argument>autoAutoWin=false</argument>
	<argument>autoZoomToFit=true</argument>
	<argument>enableLocalFileLoad=true</argument>
	<argument>enableLoadByURL=true</argument>
	<argument>scanZipFilesOnStartup=true</argument>
	<argument>enableLogOnConsole=false</argument>	
	<argument>windowLevelPreset_CT_0=350, 40</argument>
	<argument>windowLevelPreset_CT_0_descr=Chest (CT)</argument>
	<argument>windowLevelPreset_CT_1=350, 40</argument>
	<argument>windowLevelPreset_CT_1_descr=Abd/Pelvis (CT)</argument>
	<argument>windowLevelPreset_CT_2=1500, -600</argument>
	<argument>windowLevelPreset_CT_2_descr=Lung (CT)</argument>
	<argument>windowLevelPreset_CT_3=80, 40</argument>
	<argument>windowLevelPreset_CT_3_descr=Brain (CT)</argument>
	<argument>windowLevelPreset_CT_4=2500, 480</argument>
	<argument>windowLevelPreset_CT_4_descr=Bone (CT)</argument>
	<argument>windowLevelPreset_CT_5=350, 90</argument>
	<argument>windowLevelPreset_CT_5_descr=Head Neck (CT)</argument>
	<argument>windowLevelPreset_MR_0=500, 250</argument>
	<argument>windowLevelPreset_MR_0_descr=Brain T1 (MR)</argument>
	<argument>windowLevelPreset_MR_1=350, 150</argument>
	<argument>windowLevelPreset_MR_1_descr=Brain T2 (MR)</argument>
	<argument>windowLevelPreset_MR_2=300, 150</argument>
	<argument>windowLevelPreset_MR_2_descr=Sag T2 (MR)</argument>
	<argument>windowLevelPreset_MR_3=500, 250</argument>
	<argument>windowLevelPreset_MR_3_descr=Head/Neck (MR)</argument>
	<argument>windowLevelPreset_MR_4=500, 250</argument>
	<argument>windowLevelPreset_MR_4_descr=Spine (MR)</argument>
	<argument>windowLevelPreset_MR_5=590, 180</argument>
	<argument>windowLevelPreset_MR_5_descr=Abd/Pelvis T1 (MR)</argument>
	<argument>windowLevelPreset_MR_6=835, 145</argument>
	<argument>windowLevelPreset_MR_6_descr=Abd/Pelvis T2 (MR)</argument>
	<argument>windowLevelPreset_US_0=190, 80</argument>
	<argument>windowLevelPreset_US_0_descr=Ultrasound [Low Contrast] (US)</argument>
	<argument>windowLevelPreset_US_1=160, 70</argument>
	<argument>windowLevelPreset_US_1_descr=Ultrasound [Medium Contrast] (US)</argument>
	<argument>windowLevelPreset_US_2=120, 60</argument>
	<argument>windowLevelPreset_US_2_descr=Ultrasound [High Contrast] (US)</argument>
</application-desc>

</jnlp>