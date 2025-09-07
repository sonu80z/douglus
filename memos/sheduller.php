<?php
    $res = file_get_contents("http://localhost/system/dispatch.php?control=SRControl&method=processSRFiles");
	echo $res;
    if ($res){
        echo "SR reports - OK";
        sleep(5);
        return 0;
    }
    else{
        echo "SR reports - failure";
        sleep(5);
        return 1;
    }
?>
