<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN">
<HTML LANG="en-US">
<head>
	<title>Easy Downloading CDBurner</title>
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
.n1{
	font-size:69pt;
	color:#d7a5b6;
}
table{border:0; cellspacing:0}
.t1 tr td{border-top: 1px gray solid; padding:5px}
h1{font-size:30pt;}
.t{font-size:20pt;}
</style>
    <meta http-equiv="Refresh" content="5; URL=http://<?php echo $_SERVER['HTTP_HOST']?>/system/dispatch.php?control=Downloads&method=getCDBurner&job_id=<?php echo $_REQUEST['job_id']; ?>">
</head>
<BODY>
	<table width="100%" height="100%">
	<tr>
		<td id="topCell">
			<div class="x-panel-bwrap" id="ext-gen13"><div class="x-panel-tbar x-panel-tbar-noheader x-panel-tbar-noborder" id="ext-gen14">
			<div class="x-toolbar x-small-editor" id="ext-comp-1011">
			<table width="100%">
				<tr>
					<td id="ext-gen24">
					<td style="width: 100%;">
						<div class="ytb-spacer" id="ext-gen32"></div>
					</td>
					<td id="ext-gen41">
					</td>
					<td>
						<span class="ytb-sep" id="ext-gen49"></span>
					</td>
					<td>
						<span class="ytb-text" id="ext-gen50">
							<div class="ico-user-login" id="login-icon">
								<span id="login-status"></span>
							</div>
						</span>
					</td>
				</tr>
			</table>
			</div>
			</div>
			<div class="x-panel-body x-panel-body-noheader x-panel-body-noborder" id="ext-gen15" style="height: auto; width: 100%;">
				<div id="study-header">
					<div class="study-header-text" style="margin-right:50px"></div>
						<img src="/img/smallLogo.jpg" class="study-header-image">
					</div>
				</div>
			</div>
		</td>
	</tr>
	<tr>
	<td height="100%">
		<div class="x-panel-mc1" style="height:100%" align="center1">
			<table width="100%">
			<tr>
				<td colspan="2">
					<h1 align="center">Burn the CD in three easy steps:<h1>
				</td>
			</tr>
			<tr>
				<td width="260px" valign="top">
					<table height="100%">
						<tr>
							<td height="40px"></td>
						</tr>
						<tr>
							<td align="center">Download hasn't started?</td>
						</tr>
						<tr>
							<td><a href="/system/dispatch.php?control=Downloads&method=getCDBurner&job_id=<?php echo $_REQUEST['job_id']; ?>">
								<img src="/system/img/download.png" width="250px" />
								</a>
							</td>
						</tr>
					</table>
				</td>
				<td>
					<table class="t1">
						<tr>
							<td width="100%">
								<span class="t">1. Insert a blank CD in your CD-ROM</span>
							</td>
							<td align="center">
								<img src="/system/img/insert_cd.gif" />
							</td>
						</tr>
						<tr>
							<td>
								<span class="t">2. Download the program</span>
							</td>
							<td align="center">
								<img src="/system/img/<?php
//$browser = get_browser(null, true);
$u_agent = $_SERVER['HTTP_USER_AGENT']; 
// Next get the name of the useragent yes seperately and for good reason
if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
{ 
    $bname = 'Internet Explorer'; 
    $ub = "MSIE"; 
    echo 'open-ie.png';
} 
elseif(preg_match('/Firefox/i',$u_agent)) 
{ 
    $bname = 'Mozilla Firefox'; 
    $ub = "Firefox"; 
    echo 'open-ff.png';
} 
elseif(preg_match('/Chrome/i',$u_agent)) 
{ 
    $bname = 'Google Chrome'; 
    $ub = "Chrome";
    echo 'open-gh2.png';
} 
else 
{ 
    echo 'open-ff.png';
} 

?>" />
							</td>
						</tr>
						<tr>
							<td>
								<span class="t">3. Execute the program</span>
							</td>
							<td align="center">
								<img src="/system/img/execute.png" />
							</td>
						</tr>
					</table>			
				</td>			
				
				
			</tr>
			</table>
		</div>
	</td>
	</tr>
	</table>

</BODY>
</HTML>