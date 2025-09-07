<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN">
<HTML LANG="en-US">
<head>
	<title><?php
require_once('../config.php');
echo $PRODUCT.' | Burn CD';
?></title>
	<link charset="utf-8" media="screen" type="text/css" href="/system/core/ext/resources/css/ext-all.css" rel="stylesheet">
	<link charset="utf-8" media="screen" type="text/css" href="/system/core/ext/resources/css/xtheme-slate.css" rel="stylesheet">
	<link charset="utf-8" media="screen" type="text/css" href="/system/css/mdi.css" rel="stylesheet">
	<link charset="utf-8" media="screen" type="text/css" href="/system/css/file-upload.css" rel="stylesheet">
<style type="text/css">
html
{
  height: 100%
}
body
{
  height: 100%; margin:1px;
}
</style>
</head>
<BODY>
	<script type="text/javascript" src="/system/core/ext/adapter/ext/ext-base.js"></script>
	<script type="text/javascript" src="/system/core/ext/ext-all.js"></script>
<table width="100%" height="100%">
<tr>
	<td id="topCell" style="text-align: center; vertical-align:middle">
		<div>
			<table width="100%">
			<tr>
				<td></td>
				<td><img id="loading_img" src="/system/ico/loading-white.gif" />&nbsp;&nbsp;	<span id="msg"></span></td>
				<td></td>
			</tr>
			</table>
		</div>
	</td>
</tr>
</table>
<script type="text/javascript">
processID=<?php echo $_REQUEST['processID'];?>;
Ext.onReady(function()
{
	// Start a simple clock task that updates a div once per second
	var checkNotification = function()
	{
		var d=new Date();

		var params = {
		control: 'BurnCDControl',
		method: 'getProcesStatus',
		process_id: processID,
		date_: '_' + d
		}
		Ext.Ajax.request({
			url: '/system/dispatch.php',
			disableCaching: true,
			params: params,
			success: function(response)
			{
				var res = Ext.decode(response.responseText);
				if (res.success)
				{
					var type	=res.status.substr(0, res.status.search(":"));
					var message	=res.status.substr(res.status.search(":")+1);
					if (type == 'COMPLETED')
						window.location.href = '/system/viewer/CDBurnerDownloadPage.php?job_id=' + message;
					if (type == 'ERROR')
					{
						document.getElementById('loading_img').style.display='none';
						document.getElementById('msg').innerHTML = '<div style="color:red; display:inline">' + message + '</div>';
					}
					if (type == 'IN_PROGRESS')
						document.getElementById('msg').innerHTML = 'Please wait, data is now preparing... (' + message + ')';
				}
				else
					Ext.Msg.error(res.error_msg);
			},
			failure: function()
			{
				//Ext.Msg.error("Failed to request");
			}
		});
	}
	
	Ext.applyIf(Ext.Msg, {
		checkOptions:function(options){
			if(typeof options == "undefined")options = {};
			if(typeof options == "string")options = {msg:options};
			return Ext.applyIf(options, {msg:'Message'});
		},
		error:function(options){
			this.show(Ext.applyIf(this.checkOptions(options), {title:'Error', buttons:Ext.Msg.OK, icon:Ext.Msg.ERROR}));
		}
	});

	var task = {
	    run: checkNotification,
	    interval: 5000 //1 second
	}
	Ext.TaskManager.start(task);
});
</script>
</BODY>
</HTML>