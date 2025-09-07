<?php
//
//dl('php_sqlite.dll');

//dl("php_pdo_mysql");
//if (extension_loaded('php_mbstring'))
//	phpinfo();
//die();
//
// home.php
//
// Home page for displaying studies received today
//
// CopyRight (c) 2003-2008 RainbowFish Software
//
session_start();
include_once($_SERVER["DOCUMENT_ROOT"]."/system/import.php");
include($_SERVER["DOCUMENT_ROOT"]."/system/config.php");
$TITLE = "Studies Received Most Recently";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title><?php print "$PRODUCT - $TITLE"; ?></title>
		<link rel="stylesheet" href="system/core/ext/resources/css/ext-all.css" type="text/css" media="screen" charset="utf-8" />
		<link rel="stylesheet" href="system/core/ext/resources/css/xtheme-slate.css" type="text/css" media="screen" charset="utf-8" />
		<link rel="stylesheet" href="system/css/mdi.css" type="text/css" media="screen" charset="utf-8" />
		<link rel="stylesheet" href="system/css/file-upload.css" type="text/css" media="screen" charset="utf-8" />
	</head>
	<body>
	<script type="text/javascript" src="https://seal.godaddy.com/getSeal?sealID=9rUBu1RXb42OLoYijKCIrLlSGodDhAx3JP33kvoesPE6PH0LXpwZm"></script>
		<div id="loading-mask"></div>
		<div id="loading">
		    <div class="loading-indicator"><img src="<?php print $LARGE_LOGO ?>"/>
			    <br />
			    <span id="loading-msg">Loading styles and images...</span>
		    </div>
		</div>
		<!--[if IE]><link rel="stylesheet" href="css/ie.css" type="text/css" media="screen" charset="utf-8" /><![endif]-->
		<script type="text/javascript">document.getElementById('loading-msg').innerHTML = 'Loading Core API...';</script>
		<!--<script type="text/javascript" src="system/core/ext/adapter/ext/ext-base.js"></script>-->
		<script type="text/javascript">
                    // bug fix in IE and Google Chrome with mainGrid Height and layouts
                    var index_php_mainGridHeight_= document.body.clientHeight;
                    document.getElementById('loading-msg').innerHTML = 'Loading UI Components...';</script>
		<script type="text/javascript" src="system/core/ext/bootstrap.js"></script>
		<script type="text/javascript">document.getElementById('loading-msg').innerHTML = 'Loading Plugins...';</script>
		<script type="text/javascript" src="system/js/Ext.ux.SearchField.js"></script>
		<script type="text/javascript" src="system/js/Ext.ux.FileUploadField.js"></script>
		<script type="text/javascript">document.getElementById('loading-msg').innerHTML = 'Initializing Application...';</script>
		<script type="text/javascript" src="system/js/mdi.js?a=<?php echo rand();?>"></script>
		<script type="text/javascript" src="system/js/mdi.study.notesAndAttachments.js"></script>
		<script type="text/javascript">document.getElementById('loading-msg').innerHTML = 'Loading Admin Module...';</script>
		<script type="text/javascript" src="system/js/Ext.ux.GridDropZone.js"></script>
		<script type="text/javascript" src="system/js/mdi.admin.js"></script>
		<script type="text/javascript" src="system/js/mdi.passwordManager.js"></script>
		<script type="text/javascript">document.getElementById('loading-msg').innerHTML = 'Validating Login...';</script>
		<script type="text/javascript" src="system/js/mdi.authenticate.js"></script>
		<script type="text/javascript">
                    Ext.onReady(mdi.init, mdi);
                    Ext.onReady(function(){ 
                        mdi.authenticate.userSession();
                        mdi.authenticate.disclaimer = "<?php print str_replace("\r\n", "",$DISCLAIMER) ?>";
                        mdi.authenticate.loginTitle = "<?php print $LOGIN_TITLE?>";
                        mdi.attachmentDirectory = "<?php print $ATTACHMENT_VIRTUAL_DIRECTORY?>";
                    })
		</script>
		<div id="study-header">
                    <img src="/img/header-left.png" class="study-header-left" />
                    <img src="/img/header-right.png" class="study-header-right" />
                    <div class="study-header-text">
                       
                    </div>
                    <div class="study-header-text2">
                        
                    </div>

		</div>
		<div id="study-view"></div>
		<div id="study-form" class="hide"></div>
		<div id="study-footer" style="height:20px">
                    <hr>
                    <div style="float:left;"><?php print $PRODUCT. " " .$VERSION ?></div>
                    <div style="float:right;"><?php print $COPYRIGHT ?></div>
		</div>
	</body>
</html>