<?php

class RestService {
    
    public function handleRequest() {
                    
        $requestAttributes = $this->getRequestAttributeArray();
        
        if ($this->methodIsDefinedInRequest()) {
            $method = $requestAttributes["method"];
            
            $serviceClass = $this->getClassContainingMethod($method);
            if ($serviceClass != null) {
                $ref = new ReflectionMethod($serviceClass, $method);
                if (!$ref->isPublic()) {
                    echo json_encode(array('error' => 'API call is invalid.'));
                    return ;
                }
                
                $params = $ref->getParameters();
                $paramCount = count($params);
                $pArray = array();
                $paramStr = "";
                
                $iterator = 0;
                
                foreach ($params as $param) {
                    $pArray[strtolower($param->getName())] = null;
                    $paramStr .= $param->getName();
                    if ($iterator != $paramCount-1) {
                        $paramStr .= ", ";
                    }
                    
                    $iterator++;
                }
                
                foreach ($pArray as $key => $val) {
                    $pArray[strtolower($key)] = $requestAttributes[strtolower($key)];
                }
                
                if (count($pArray) == $paramCount && !in_array(null, $pArray)) {//echo  get_class($serviceClass),$method;
                    //$studyid=$pArray['studyid'];
                    $result = call_user_func_array(array($serviceClass, $method), $pArray);
                    
                    if ($result != null) {
                        if($method == "getAjaxService")
                            echo $result;
                            else
                                echo json_encode($result);
                    }
                }
                else {
                    echo json_encode(array('error' => "Required parameter(s) for ". $method .": ". $paramStr));
                }
            }
            else {
                echo json_encode(array('error' => "The method " . $method . " does not exist."));
            }
        }
        else {
            echo json_encode(array('error' => 'No method was requested.'));
        }
        
        exit;
    }
    public function getAjaxService(){
        $methods = get_class_methods($this);
        $ajaxService = "";
        $publicMethods = array();
        $serviceURL =  strtok($_SERVER["REQUEST_URI"],'?');
        $serviceSignature = "var ". get_class($this) ."Service = (function($){".
            "\n\tfunction invoke(params, success){\n\t\t$.ajax({url:\"". $serviceURL ."\", \n\t\t\t\tdata:params, \n\t\t\t\tdataType:\"json\", \n\t\t\t\tcontentType:\"application/json\", \n\t\t\t\ttype:\"GET\",  \n\t\t\t\tsuccess:success});\n\t}" .
            "\n\treturn {";
        for($i = 0; $i < sizeof($methods);$i++){
            $ref = new ReflectionMethod($this, $methods[$i]);
            if ($ref->isPublic() && !$ref->isStatic() && $methods[$i] != "handleRequest" && $methods[$i] != "getAjaxService") {
                //add service call.
                $methodSignature = "\n\t\t" . $methods[$i] . ": function(";
                
                $paramList = $ref->getParameters();
                $params = array();
                $paramData = array();
                foreach ($paramList as $param) {
                    $params[sizeof($params)] = $param->getName();
                    $paramData[sizeof($params)] = "\"" . $param->getName() . "\":" . $param->getName();
                }
                $methodSignature .= implode(",", $params) . ", success){";
                //process params and invoke service call
                
                $methodSignature .= "\n\t\t\tinvoke({method:\"" .$methods[$i] . "\",". implode(",", $paramData)  .", cache:(new Date()).getTime()}, success);";
                $methodSignature .= "\n\t\t}";
                $publicMethods[sizeof($publicMethods)] = $methodSignature;
            }
        }
        $serviceSignature .= "" . implode(",", $publicMethods) . "\n\t}\n})(jQuery);";
        return $serviceSignature;
    }
    private  function getClassContainingMethod($method) {
        if ($this->methodExists($method)) {
            return $this;
        }
        return null;
    }
    private function methodExists($method){
        if(!method_exists($this, $method)){
            foreach(class_parents($object) as $parent)
            {
                if(method_exists($parent, $method))
                {
                    return true;
                }
            }
            return false;
        }else{
            return true;
        }
    }
    
    private function methodIsDefinedInRequest() {
        return array_key_exists("method", $this->getRequestAttributeArray());
    }
    
    private function getRequestAttributeArray() {
        return array_change_key_case($_REQUEST, CASE_LOWER);
    }
}