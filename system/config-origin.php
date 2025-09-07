<?php
session_cache_limiter('private');
session_start();
/**********************************************************
 *--------------- NOTICE DON'T FORGET --------------------*
 **********************************************************/
 //TURN OFF NOTICES IN PHP WHICH IS error_reporting = E_ALL & ~E_NOTICE
 //YOU MIGHT NEED TO CHANGE DATABASE
 //IF THIS IS ONLY BE SERVED FROM HTTPS THEN YOU WILL NEED TO ADD THAT TO HOST

/**********************************************************
/* SITE CONFIGURATION                                     *
/**********************************************************/
$PROTOCOL = "https://";
if ( !empty($_SERVER['HTTPS']) ) $PROTOCOL = "https://";

$HOST = $PROTOCOL.$_SERVER['HTTP_HOST']."/";

$PRODUCT = "Tech Care X-Ray";
// mail from, etc
$PRODUCT_NAME = "Tech Care X-Ray";
$CONTACT_PHONE = '555.555.5555';
$VERSION = "v4.0";
$COPYRIGHT = "Copyright (c) 2006-".date('Y');
$INSTALL_DIRECTORY = $_SERVER["DOCUMENT_ROOT"];
$TRANSCRIPTION_DIRECTORY = $INSTALL_DIRECTORY.'transcriptions/';
$ORDER_DIRECTORY = $INSTALL_DIRECTORY.'orders/';
$ATTACHMENT_DIRECTORY = $INSTALL_DIRECTORY.'attachments/';
//this should match what the alias is in the apache server config.
$TRANSCRIPTION_VIRTUAL_DIRECTORY = $HOST."transcriptions/";
//this should match what the alias is in the apache server config.
$ATTACHMENT_VIRTUAL_DIRECTORY = $HOST.'attachments/';
//this should match what the default password has pdfs
$PDF_DEFAULT_PASSWORD='Password'; 
// possible values is 'native_exec' or 'windows_com_exec'. 'windows_com_exec' works more slow, but on all servers
$PDF_TYPE_PROCESSING='windows_com_exec';
/**********************************************************
/* SEARCH CONFIGURATION                                   *
/**********************************************************/

/* This option allows you to search x number of months back
 * if there is no results for today it will use this to search
 * from this number of months back.
 */
$SEARCH_LAST_MONTHS = 1;

/**********************************************************
/* HOME CONFIGURATION                                     *
/**********************************************************/
$SMALL_LOGO = "img/smallLogo.jpg";
$LARGE_LOGO = "img/largelogo.jpg";

/**********************************************************
/* DATABASE CONFIGURATION                                 *
/**********************************************************/
$DB_HOST = "localhost";
$DB_USER =  "root";
$DB_PASS = "662smain";
$DB_DATABASE = "archive";
/**********************************************************
/* EVENT CONFIGURATION                                    *
/**********************************************************/
$EVENT_USER_AUTH = "User Authentication";
$EVENT_VIEW_STUDY = "View Study";
/**********************************************************
/* DICOM VIEWER CONFIGURATION                             *
/**********************************************************/
//this should be the ip of the server, remove /referring/ if that's not part of the path
$DW_ServerSciptPath = $HOST;
// this shouldn't change unless you change the directory name for the viewer
$DW_DicomViewerPath = "system/viewer/";
// this is the path to transcription documents... if this is changed to absolute then remove ../ from the beggining
$DW_PDFReportPath = $TRANSCRIPTION_DIRECTORY;
// this is so we can redirect back to the study page
$DW_StudyPath = "index.php";

/**********************************************************
/* MAIL CONFIGURATION			                           *
/**********************************************************/
$MAIL_FROM = "dpotter@cnymail.com";
$MAIL_MARK_THE_STUDY_AS_REVIEWED = true;
$MAIL_FOR_PHYSICIAN_TEMPLATE = "Dr. {physician_name} physician, 

You have a study with Critical Results. Please login to the web-portal (http://".$_SERVER['HTTP_HOST'].") and 
review the Study as soon as possible.    

Note:   studies with Critical Results will be at the top of the list with an \"!\" in the first 
Column.   Once you have reviewed the study,  please Right Click on the study and click on 
\"mark as reviewed\"!  

We appreciate your attention to this matter. 

Sincerely, 

Site Administrator";

/**********************************************************
/* BURNCD CONFIGURATION                           *
/**********************************************************/
$PACSONE_EXPORT_PATH = "C:/Program Files/PacsOne/export/";


/**********************************************************
/* AUTHENTICATION CONFIGURATION                           *
/**********************************************************/
$LOGIN_TITLE = "Tech Care X-ray";
$DISCLAIMER = <<<HEREDOC
<p>SOFTWARE LICENSE/USE AGREEMENT FOR 
</p><br><p>PHYSICIAN REVIEW SERVICE Software
</p><br><p>
BY ELECTRONICALLY CLICKING THE \"I ACCEPT\" BUTTON YOU (\"YOU\" OR \"YOUR\") ACCEPT AND AGREE TO BE BOUND BY THE FOLLOWING TERMS AND CONDITIONS OF THIS AGREEMENT.
</p><br><p>
IF YOU DO NOT AGREE TO ALL OF THE TERMS OF THIS AGREEMENT, CLICK THE \"DECLINE\" BUTTON AND THE INSTALLATION PROCESS WILL NOT CONTINUE.  
</p><br><br><p>
Single User License Grant: Mobile Digital Imaging, Inc. and its suppliers grant to Customer (\"Customer\") a nonexclusive and nontransferable license to use the Physician Review Service (\"PRS\") in object code form solely on a single central processing unit owned or leased by Customer or otherwise embedded in equipment provided by Mobile Digital Imaging, Inc.  
</p><p>
Below are stated the licensing provisions of Mobile Digital Imaging, Inc., owner of all intellectual property rights as vested in the software PRS. The End User is bound to these conditions when using the PRS software.  
</p><p>  
1) End-User shall mean the party which settled with Mobile Digital Imaging Inc for delivery and/or a support agreement in relation to the Software, as described hereinafter.  
</p><p>
2) The term the \"Software\" has the following meaning: the commercially available version of the software known as PRS, its supporting information, documentation and manuals are for REVIEW PURPOSES ONLY, AND ARE NOT INTENDED FOR DIAGNOSTIC INTERPRETATION. The source code of the Software will not be supplied.
</p><p>
3) The term the \"Installation\" has the following meaning: The date that the connectivity, the operational testing and verification of the Software at the location of End-User have been carried out satisfactory.
</p><p>
4) Upon condition that all agreed license fees for the use of the software are paid to Mobile Digital Imaging, Mobile Digital Imaging Inc hereby grants to End-User and End-User hereby accepts from Mobile Digital Imaging Inc a non-transferable and non-exclusive license to use the Software on one processing unit as an end-user for the internal business of End-User, for the sole benefit of the End-User. End-User is not entitled to use the Software for commercial purposes. Third parties shall not be permitted by End-User to use or have access to the Software. End-User is only allowed to permit use to its Radiologists, Referring Physicians, Referring Facilities, and Staff.
</p><p>
5) End-User will not be entitled to sell, sub-license, let out or alienate the Software or transfer it by way of security, or to put it at disposal of any third party in any way whatsoever.
</p><p>
6) In consideration of the rights as granted, End-User will pay to Mobile Digital Imaging, Inc. a license fee as agreed between it and End-User. End-User will pay Mobile Digital Imaging, Inc. separately for the support as to the Software if a support subscription has been agreed upon.
</p><p>
7) End-User shall not modify, adapt, dissemble, translate, vary, copy, reproduce or alter the Software in any way, without the prior written approval of Mobile Digital Imaging Inc. End-User is not allowed to copy or reproduce the Software for any purpose, 
</p><p>
8) If any failure to conform such specifications appears in the warranty period of twelve months as of Installation, Mobile Digital Imaging, Inc. shall, according to the terms and conditions of the agreed support agreement, use its best efforts to provide valuable assistance (correction or replacement). In case a correction is not possible within a short time Mobile Digital Imaging Inc shall use its best endeavors to provide a provisional solution without any undue delay. Mobile Digital Imaging Inc may charge the costs of repair in the event of errors made by End-User or any other cause for which Mobile Digital Imaging Inc cannot be blamed Recovery of any data which may have been lost will not be covered by the warranty.
</p><p>
9) The warranty as indicated shall be limited in such a way that Mobile Digital Imaging Inc shall not be liable for any malfunction or error resulting from a modification made with or without the prior written explicit approval Mobile Digital Imaging Inc or resulting from improper use of the Software.
</p><p>
10) In no event shall Mobile Digital Imaging Inc be liable for any loss, damage or expense whatsoever including, without limitation, time, money or good-will arising from or in connection with the use, non-use, performance or non-performance or inability to use, and operation of the Software.
</p><p>
11) Mobile Digital Imaging Inc’s sole and exclusive liability is to provide programming services to replace or correct defects in the Software which cause the Software to fail to conform to Mobile Digital Imaging Inc’s warranty set forth in this Agreement. Mobile Digital Imaging Inc disclaims any and all implied warranties, except to the extent that it is unlawful to exclude such liability.
</p><p>
12) In no event shall Mobile Digital Imaging Inc be liable to End-User for incidental, exemplary, punitive or consequential damages for any reason whatsoever, except to the extent that it is unlawful to exclude such liability. End-User hereby waives, for itself and its successors and assigns, any and all claims for direct, special, incidental, or consequential damages.
</p><p>
13) Mobile Digital Imaging Inc expressly excludes any kind of liability for damages, including but not limited to death or bodily injury, incurred because of medical decisions or refraining of the same based upon use of the Software. End-User acknowledges that Mobile Digital Imaging Inc is never responsible/liable for any consequence due to any medical decision or refraining from any decision based upon use of the Software.
</p><p>
14) In the event that a limitation of liability in the above said warranty provisions (12- 16) shall be held to be invalid for any reason and Mobile Digital Imaging Inc becomes liable for loss or damage that would otherwise have been excluded, such total liability shall never exceed the amount of the license fee per event, a series of interconnected events being regarded as a single event.
</p><p>
15) In the event Mobile Digital Imaging Inc or their employees commits any tort for which Mobile Digital Imaging Inc can be held liable in law, Mobile Digital Imaging Inc will only be liable to make compensation for any loss caused by death or bodily injury and for any other loss as far as these have arisen through willful intent or gross negligence. In these cases damages will not exceed $5,000 USD per event causing a loss, a series of interconnected events being regarded as a single event.
</p><p>
16) End-User and its employees shall keep strictly confidential any and all information relating to the Software and other information, as confidentially disclosed End-User shall take all necessary steps to ensure the confidentiality of the Software.
</p><p>
17) This license shall be governed by the laws of USA within the jurisdiction of the competent court of the State of New York.
</p><p>
18) End-User confirms and acknowledges that any and all of the trademark(s), trade names, copyrights, patents and other intellectual property rights used or embodied in or in connection with the Software (including any future additions, releases, enhancements, updates, translations or modifications) shall be and remain the sole property of Mobile Digital Imaging Inc.
</p><p>
19) Mobile Digital Imaging Inc may terminate this license by a one (1) month prior written notice upon failure of End-User to remedy a breach of any of its obligations hereunder within ten (10) days of being notified of such breach. End-User shall discontinue all use of the Software and return the Software to Mobile Digital Imaging, Inc. and not keep any copies of the Software. Under no circumstances End-User shall be entitled to claim any indemnification as a result of the termination of this license. In the event of bankruptcy of End-User, this license is terminated automatically with immediate effect and the Software shall be returned without any undue delay.
</p>
HEREDOC;
?>