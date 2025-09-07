<?php
// this file has 3 tasks:
// 1. downloading files from the sftp server
// 2. processing files (this file creats PDF file from the *.txt)
// 3. marks processed study as rewived
//  runs from the sheduled tasks


    $sftp_config['hostname'] = 'Sftp.onradinc.com';
    $sftp_config['username'] = 'mmds';
    $sftp_config['password'] = 'B0st0n';
    $sftp_config['debug'] = TRUE;

    
    $txtFilesSharedPath = $_SERVER["DOCUMENT_ROOT"].'/etc/shared_folder';
    $txtFilesProcessedPath = $txtFilesSharedPath.'/processed';
    $txtFilesBADPath = $txtFilesSharedPath.'/bad';

    function log_message($type, $text)
    {
//        echo "$type, $text";
    }
    
    function _error_log($message)
    {
        $f = fopen('log.log', 'a');
        fwrite($f, date("F j, Y, g:i a").$message);
        fclose($f);
        die($message);
    }
    
    if (isset ($_REQUEST['operation']) && $_REQUEST['operation'] == 'download' )
    {
        require_once ('../utilities/sftp.php');
         $sftp = new Sftp();

        $res = $sftp->connect($sftp_config);
        if (!$res)
        {
            _error_log("could not connect to the sftp server");
        }


        $list = $sftp->list_files("/", TRUE);

        if (!is_array($list))
        {
           _error_log("could not list files on the server");

        }

        $c = count ($list);
        for ($i = 0; $i < $c; $i++)
        {
            set_time_limit(15);
            $res = $sftp->download("/".$list[$i], $txtFilesSharedPath."/".$list[$i]);
            if (!$res)
            {
                echo ( "could not download the file ".$list[$i]);
            }
            else
            {
                $sftp->delete_file("/".$list[$i]);
//            echo "/".$list[$i]." -- deleted\n";
            }
        }
        echo 'OK';
    }
    else
    {
        require_once ('../import.php');
        require_once ('PDFControl.php');
        require_once('../config.php');
        import('system.logger');

    // input folder path with txt files (for converting txt files to pdfs)
    // implemented only for 1
        $control = new PDFControl();
        if ($handle = opendir($txtFilesSharedPath)) 
        {
                    while (false !== ($file = readdir($handle))) 
            {
                if ($file != "." && $file != ".." && is_file($txtFilesSharedPath.'/'.$file)) 
                {
                    set_time_limit(35);
                    $res = false;
                    $p = array('filename' => $txtFilesSharedPath.'/'.$file);
                    $_logEvent = array();
                    $_logEvent['event_type'] = 'TXT file';
                    try
                    {
                        $res = $control->processTextFile($p);
                    }
                    catch (Exception $e)
                    {
                        $res = false;
                    }

                    if (!$res)
                    {
                        $_logEvent['additional_text'] = "Could not import the file $file: ".$e->getMessage();

                        if(strpos($_logEvent['additional_text'], 'Could not find a study for patient') > 0)
                        {
//                                die('111');
								if(copy($txtFilesSharedPath.'/'.$file, $txtFilesBADPath.'/'.$file))
                                        unlink($txtFilesSharedPath.'/'.$file);
                        }

                        if(strpos($_logEvent['additional_text'], 'Could not fetch the patientID by next data') > 0)
                        {
//                                die('222');
                                if(copy($txtFilesSharedPath.'/'.$file, $txtFilesBADPath.'/'.$file))
                                        unlink($txtFilesSharedPath.'/'.$file);
                        }
                        logger::log($_logEvent);
                        die('error desription: '.$_logEvent['additional_text'] );
                    }
                    else
                    {
 //                               die('333');
                        $_logEvent['additional_text'] = "File $file imported";
                        logger::log($_logEvent);

                        if(copy($txtFilesSharedPath.'/'.$file, $txtFilesProcessedPath.'/'.$file))
                            unlink($txtFilesSharedPath.'/'.$file);
                    }                    
                    echo "$_logEvent[additional_text] \n";   
    //                $p['output']
                }
            }
            closedir($handle);
        }
    }
    
    
//    
//    
//    
?>
