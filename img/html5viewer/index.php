<?php 
include_once($_SERVER["DOCUMENT_ROOT"] . "/system/config.php");
$studyReport = $_REQUEST['entry'] . ".pdf";
$studyReportPath = $TRANSCRIPTION_DIRECTORY . "/" . $studyReport;
$studyReportURL = $TRANSCRIPTION_VIRTUAL_DIRECTORY . "/" . $studyReport;
$studyReportExists = file_exists($studyReportPath);
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		
		<title>Dicom HTML 5 Viewer</title>
		<!-- meta -->
		<meta charset="utf-8" />
		<meta name="description" content="Dicom Viewer" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />

		<!-- css -->
		<link rel="stylesheet" href="assets/css/bootstrap.min.css" />
		<link rel="stylesheet" href="assets/css/font-awesome.min.css" />
		<link rel="stylesheet" href="assets/css/ace-fonts.css" />
		<link rel="stylesheet" href="assets/css/ace.min.css" />
		<link rel="stylesheet" href="assets/css/ace-rtl.min.css" />
		<link rel="stylesheet" href="assets/css/ace-skins.min.css" />
		<link rel="stylesheet" href="assets/css/jquery.nouislider.min.css" />
		<link rel="stylesheet" href="assets/css/html5viewer.css" />
		<link rel="stylesheet" href="assets/css/vex.css" />
		<link rel="stylesheet" href="assets/css/vex-theme-default.css" />
	</head>
	<body class="skin-1" style="height:100%" >
	<div class="knob-container" style="position:absolute;z-index:99999;display:none;">
		<span>Loading Images</span>
		<input class="knob"  data-width="150" data-readonly=true data-fgColor="#2c6aa0" data-displayInput=true value="0"></input>
	</div>
	<div class="debug-window" style="display:none;" >
			<a href="javascript:void(0);"><i class="fa fa-bug"></i></a>
			<ul class="nav nav-list debug-messages hidden">
				<li>
					<a href="javascript:void(0);" class="dropdown-toggle">
						<span class="menu-text"> Coords </span>
						<b class="arrow fa fa-angle-down"></b>
					</a>
					<ul class="debug-coords" style="display:none;">
						<li style="color:white;">You must drag on the image before you can see the coords</li>
					</ul>
				</li>
				<li>
					<a href="javascript:void(0);" class="dropdown-toggle">
						<span class="menu-text"> Center Calculation </span>
						<b class="arrow fa fa-angle-down"></b>
					</a>
					<ul class="debug-center-calc" style="display:none;">
					</ul>
				</li>
				<li>
					<a href="javascript:void(0);" class="dropdown-toggle">
						<span class="menu-text"> Log </span>
						<b class="arrow fa fa-angle-down"></b>
					</a>
					<ul class="debug-log" style="display:none;">
					</ul>
				</li>
				<li>
					<a href="javascript:void(0);" class="dropdown-toggle">
						<span class="menu-text"> Actions </span>
						<b class="arrow fa fa-angle-down"></b>
					</a>
					<ul class="debug-actions" style="display:none;">
						<li><a class="debug-action-calc-center" href="javascript:void(0);">Recalculate Center</a></li>
					</ul>
				</li>
			</ul>
		</div>
		<div id="actions" style="display:none;">
			
		</div>
		<div class="navbar navbar-default" id="navbar">
			<div class="navbar-container" id="navbar-container">
				<div class="navbar-header pull-left">
					<a href="<?php echo $HOST . "index.php" ?>" class="navbar-brand">
						<i class="fa fa-arrow-circle-o-left" title="Go Back" style="font-size:32px;"></i>
						<ul class="patient-info">
		               		<li>
				              	<label class="patient-name-label">
				              		<strong>Patient:</strong>
				              		<span class="patient-name"></span>
				              	</label>
		            		</li>
		            		<li>
								<label class="patient-dos-label">
									<strong>DOS:</strong>
									<span class="patient-dos"></span>
								</label>
							</li>
							<li>
								<label class="patient-dob-label">
									<strong>DOB:</strong>
									<span class="patient-dob"></span>
								</label>
							</li>
							<li>
								<label class="patient-id-label">
									<strong>ID:</strong>
									<span class="patient-id"></span>
							    </label>
							</li>
		            	</ul>
					</a><!-- /.brand -->
				</div><!-- /.navbar-header -->
			</div>
		</div>
		
		<div class="main-container"  style="height:100%">
			<div class="main-container-inner">
				<a class="menu-toggler" id="menu-toggler" href="#">
					<span class="menu-text"></span>
				</a>
				<div class="sidebar col-md-3 col-sm-3 col-xs-5" style="height:100%" id="sidebar">
					<div class="sidebar-shortcuts" id="sidebar-shortcuts">

						<div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
							<span class="btn btn-danger"></span>

							<span class="btn btn-danger"></span>

							<span class="btn btn-danger"></span>

							<span class="btn btn-danger"></span>
						</div>
					</div><!-- #sidebar-shortcuts -->
					<ul class="nav nav-list">

						<li>
							<a href="#" class="dropdown-toggle">
								<i class="fa fa-wrench"></i>
								<span class="menu-text"> Tools </span>
								<b class="arrow fa fa-angle-down"></b>
								
							</a>
							<ul class="submenu">
									<li class="sidebar-tool-buttons first">
										<button class="btn  btn-danger rotate-left" title="Rotate Left">
											<i class="fa fa-rotate-left"></i>
										</button>

										<button class="btn  btn-danger rotate-right" title="Rotate Right">
											<i class="fa fa-rotate-right"></i>
										</button>

										<button class="btn  btn-danger flip-vertical" title="Flip Vertically">
											<i class="fa fa-arrows-v"></i>
										</button>

										<button class="btn  btn-danger flip-horizontal" title="Flip Horizontally">
											<i class="fa fa-arrows-h"></i>
										</button>
									</li>
									<li class="sidebar-tool-buttons">
										<button class="btn btn-danger pan" title="Pan">
											<i class="fa fa-arrows-alt"></i>
										</button>
										<button class="btn btn-primary window-level" title="Window/Level">
											<i class="fa fa-adjust"></i>
										</button>
										<button class="btn btn-danger measure" title="Measure">
											<i class="icon-ruler"></i>
										</button>
										<button class="btn btn-danger clear-measurements" title="Clear Measurements">
											<i class="icon-ruler-clear"></i>
										</button>
									</li>
									<li class="sidebar-tool-buttons ">

										<button class="btn btn-danger cine" title="Cine">
											<i class="fa fa-play"></i>
										</button>

										<button class="btn btn-danger configure-windows" title="Configure Windows">
											<i class="fa fa-columns"></i>
										</button>

										<button class="btn btn-danger reset" title="Reset">
											<i class="fa fa-refresh"></i>
										</button>
									</li>
									<li class="sidebar-tool-buttons clearfix">
										

										<span style="color:white;">Zoom: <span id="zoom-value"></span></span>
										<br/>
										<div id="zoom-slider" ></div>
										<button class="btn btn-zoom-decrease btn-danger" title="Decrease Zoom">
											<i class="fa fa-minus"></i>
										</button>
										<button class="btn btn-zoom-increase  btn-danger" title="Increase Zoom">
											<i class="fa fa-plus"></i>
										</button>

									</li>
									<li class="sidebar-tool-buttons clearfix">

										<span style="color:white;">Sharpness: <span id="sharpness-value"></span></span>
										<br/>
										<div id="sharpness-slider" ></div>
										<button class="btn  btn-sharp-decrease btn-danger" title="Decrease Sharpness">
											<i class="fa fa-minus"></i>
										</button>
										<button class="btn  btn-sharp-increase btn-danger" title="Increase Sharpness">
											<i class="fa fa-plus"></i>
										</button>
									</li>
								</ul>
						</li>
						<li>
							<a href="#" class="dropdown-toggle">
								<i class="fa fa-desktop"></i>
								<span class="menu-text"> Series </span>
								<b class="arrow fa fa-angle-down"></b>
							</a>

							<ul class="submenu series-info" >
							</ul>
						</li>
						<li>
							<a data-toggle="dropdown" class="dropdown-toggle study-info" href="#" >
								<i class="fa fa-book"></i>
								<span class="menu-text"> Study Info </span>
							</a>

							<ul class=" dropdown-navbar dropdown-menu dropdown-close">
								<li class="dropdown-header">
									<i class="fa fa-user"></i>
									Patient Info
								</li>
								<li>
									<a>
										<div class="clearfix">
											<span class="pull-left" >Patient Name:</span>
											<span class="pull-right study-info-value" data-bind="ImageInfo.PatientsName">N/A</span>
										</div>
									</a>
								</li>
								<li>
									<a>
										<div class="clearfix">
											<span class="pull-left" >Patient Birthday:</span>
											<span class="pull-right study-info-value" data-bind="ImageInfo.PatientsBirthDate">N/A</span>
										</div>
									</a>
								</li>
								<li class="dropdown-header">
									<i class="fa fa-book"></i>
									Study Info
								</li>
								<li>
									<a>
										<div class="clearfix">
											<span class="pull-left">Study UID:</span>
											<span class="pull-right study-info-value" data-bind="Study.UID">N/A</span>
										</div>
									</a>
								</li>
								<li>
									<a>
										<div class="clearfix">
											<span class="pull-left">Institution Name:</span>
											<span class="pull-right study-info-value" data-bind="ImageInfo.InstitutionName">N/A</span>
										</div>
									</a>
								</li>
								<li>
									<a>
										<div class="clearfix">
											<span class="pull-left">Modality:</span>
											<span class="pull-right study-info-value" data-bind="ImageInfo.Modality">N/A</span>
										</div>
									</a>
								</li>
								<li>
									<a>
										<div class="clearfix">
											<span class="pull-left">Study Description:</span>
											<span class="pull-right study-info-value" data-bind="ImageInfo.StudyDescription">N/A</span>
										</div>
									</a>
								</li>
								<li>
									<a>
										<div class="clearfix">
											<span class="pull-left">Body Part Examined:</span>
											<span class="pull-right study-info-value" data-bind="ImageInfo.BodyPartExamined">N/A</span>
										</div>
									</a>
								</li>
								<li>
									<a>
										<div class="clearfix">
											<span class="pull-left">Referring Physician's Name:</span>
											<span class="pull-right study-info-value" data-bind="ImageInfo.ReferringPhysiciansName">N/A</span>
										</div>
									</a>
								</li>
								<li>
									<a>
										<div class="clearfix">
											<span class="pull-left">Study Date:</span>
											<span class="pull-right study-info-value" data-bind="ImageInfo.StudyDate">N/A</span>
										</div>
									</a>
								</li>
								<li>
									<a>
										<div class="clearfix">
											<span class="pull-left">Study Time:</span>
											<span class="pull-right study-info-value" data-bind="ImageInfo.StudyTime">N/A</span>
										</div>
									</a>
								</li>
								<li>
									<a>
										<div class="clearfix">
											<span class="pull-left">Acquisition Date:</span>
											<span class="pull-right study-info-value" data-bind="ImageInfo.AcquisitionDate">N/A</span>
										</div>
									</a>
								</li>
								<li>
									<a>
										<div class="clearfix">
											<span class="pull-left">Acquisition Time:</span>
											<span class="pull-right study-info-value" data-bind="ImageInfo.AcquisitionTime">N/A</span>
										</div>
									</a>
								</li>
								<li class="dropdown-header">
									<i class="fa fa-desktop"></i>
									Series Info
								</li>
								<li>
									<a>
										<div class="clearfix">
											<span class="pull-left">Series UID:</span>
											<span class="pull-right study-info-value"  data-bind="Series.UID" >N/A</span>
										</div>
									</a>
								</li>
								<li>
									<a>
										<div class="clearfix">
											<span class="pull-left">Series Description:</span>
											<span class="pull-right study-info-value" data-bind="ImageInfo.SeriesDescription">N/A</span>
										</div>
									</a>
								</li>

								<li class="dropdown-header">
									<i class="fa fa-picture-o"></i>
									Image Info
								</li>
								<li>
									<a>
										<div class="clearfix">
											<span class="pull-left">Instance Create Date:</span>
											<span class="pull-right study-info-value" data-bind="ImageInfo.InstanceCreationDate">N/A</span>
										</div>
									</a>
								</li>
								<li>
									<a>
										<div class="clearfix">
											<span class="pull-left">Instance Create Time:</span>
											<span class="pull-right study-info-value" data-bind="ImageInfo.InstanceCreationTime">N/A</span>
										</div>
									</a>
								</li>
								<li>
									<a>
										<div class="clearfix">
											<span class="pull-left">Window Center:</span>
											<span class="pull-right study-info-value" data-bind="ImageInfo.WindowCenter">N/A</span>
										</div>
									</a>
								</li>
								<li>
									<a>
										<div class="clearfix">
											<span class="pull-left">Window Width:</span>
											<span class="pull-right study-info-value" data-bind="ImageInfo.WindowWidth">N/A</span>
										</div>
									</a>
								</li>
							</ul>
						</li>
						<li>
							<a class="study-report" href="<?php if(!$studyReportExists) echo 'javascript:void(0);'; else echo $studyReportURL; ?>"  target='_blank' >
								<i class="fa fa-file"></i>
								<span class="menu-text" title="<?php if(!$studyReportExists) echo 'No Study Report Available'; ?>"> Study Report </span>
								<?php if($studyReportExists){ ?>
									<span class="badge badge-important">View</span>
								<?php }else{?>
									<span class="badge badge-important">N/A</span>
								<?php }?>
							</a>
						</li>
						
					</ul><!-- /.nav-list -->
				</div><!-- /.side-bar -->
			</div><!-- /.main-content-inner -->
		</div><!-- /.main-container -->
		<div class="viewer-layout " style="height:100%">
			<div class="viewer ">
				<div class="viewer-hud">
					<span>C:</span> <span class="level-value"></span><br/>
					<span>W:</span> <span class="window-value"></span><br/>
					<span>Zoom:</span> <span class="zoom-value"></span>%<br/>
					<span>Series:</span> <span class="series-value"></span><br/>
				</div>

				<i class="fa fa-arrow-circle-left prev-image" style="position:absolute;font-size:42px;color:#fff;"></i>
				<i class="fa fa-arrow-circle-right next-image" style="position:absolute;font-size:42px;color:#fff;"></i>
				<canvas class="active"></canvas>
			</div>
			<div class="viewer hidden">
				<div class="viewer-hud">
					<span>C:</span> <span class="level-value"></span><br/>
					<span>W:</span> <span class="window-value"></span><br/>
					<span>Zoom:</span> <span class="zoom-value"></span>%<br/>
					<span>Series:</span> <span class="series-value"></span><br/>
				</div>
				<i class="fa fa-arrow-circle-left prev-image" style="position:absolute;font-size:42px;color:#fff;"></i>
				<i class="fa fa-arrow-circle-right next-image" style="position:absolute;font-size:42px;color:#fff;"></i>
				<canvas ></canvas>
			</div>
			<div class="viewer hidden">
				<div class="viewer-hud">
					<span>C:</span> <span class="level-value"></span><br/>
					<span>W:</span> <span class="window-value"></span><br/>
					<span>Zoom:</span> <span class="zoom-value"></span>%<br/>
					<span>Series:</span> <span class="series-value"></span><br/>
				</div>
				<i class="fa fa-arrow-circle-left prev-image" style="position:absolute;font-size:42px;color:#fff;"></i>
				<i class="fa fa-arrow-circle-right next-image" style="position:absolute;font-size:42px;color:#fff;"></i>
				<canvas ></canvas>
			</div>
			<div class="viewer hidden">
				<div class="viewer-hud">
					<span>C:</span> <span class="level-value"></span><br/>
					<span>W:</span> <span class="window-value"></span><br/>
					<span>Zoom:</span> <span class="zoom-value"></span>%<br/>
					<span>Series:</span> <span class="series-value"></span><br/>
				</div>
				<i class="fa fa-arrow-circle-left prev-image" style="position:absolute;font-size:42px;color:#fff;"></i>
				<i class="fa fa-arrow-circle-right next-image" style="position:absolute;font-size:42px;color:#fff;"></i>
				<canvas ></canvas>
			</div>
		</div>

		<script type="text/javascript">
			window.jQuery || document.write("<script src='assets/js/jquery-2.0.3.min.js'>"+"<"+"/script>");
		</script>

		<!-- <![endif]-->

		<!--[if IE]>
			<script type="text/javascript">
			 window.jQuery || document.write("<script src='assets/js/jquery-1.10.2.min.js'>"+"<"+"/script>");
			</script>
		<![endif]-->
		<script type="text/javascript">
			if("ontouchend" in document) document.write("<script src='assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
		</script>
		<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="assets/js/vex.combined.min.js"></script>
		<script>
			var studies = '<?php echo $_REQUEST['entry'];?>';
			var studyReportExists = '<?php echo $studyReportExists;?>';
			var rootURL = '<?php echo $HOST . "index.php" ?>';
		</script>
		<script src="/system/html5viewer/assets/js/jquery.nouislider.min.js"></script>
		<script src="/system/html5viewer/assets/js/jquery.knob.min.js"></script>
		<!-- ace scripts -->
		<script type="text/javascript" src="assets/js/ace.min.js"></script>
		<script type="text/javascript" src="/system/html5viewer/api.php?method=getAjaxService"></script>
		<!-- custom scripts -->
		<script type="text/javascript" src="assets/js/jquery.cfx.js"></script>
		<script type="text/javascript" src="assets/js/html5viewer.js"></script>


	</body>
</html>