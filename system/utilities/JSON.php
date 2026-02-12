<?php

class JSON {

    static function Encode($obj) {
//        return json_encode($obj);
        $json = array();
        foreach ($obj as $col => $val) {
            if (is_array($val))
                $val = JSON::Encode($val);
            else if (is_string($val) && !is_numeric($val) && !preg_match("#(false|true)#is", $val))
                $val = "'" . addslashes($val) . "'";
            array_push($json, strtolower($col) . ":" . $val . "");
        }
        return "{" . join(",",$json) . "}";
    }

}

?>