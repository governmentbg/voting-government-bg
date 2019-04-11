<?php

if(!function_exists('api')){

    /**
     * Helper function to get data from own API.
     * @desc api(Api\UserController::class, 'resetPassword')
     * @param string $class
     * @param string $method_name
     * @param array $params
     * @param string $httpMethod
     * @return array
     */
    function api($class, $method_name, $params = true, $httpMethod = 'POST'){
        if($params === true){
            $params = request()->all();
        }

        $request = Request::create('', $httpMethod, $params);

        $api = app()->make($class);
        if(method_exists($api, $method_name)){
            return $api->{$method_name}($request)->getData();
        }

        logger()->error('Method ' . $method_name . ' does not exist on class ' . $class);
        return ['succes ' => false, 'status' => 500];
    }
}

if(!function_exists('api_result')){

    /**
     * Helper function to get data from own API.
     * @desc api(Api\UserController::class, 'resetPassword')
     * @param string $class
     * @param string $method_name
     * @param array $params
     * @param string $httpMethod
     * @return array
     */
    function api_result($class, $method_name, $params = true, $resultKey = null, $httpMethod = 'POST'){
        $result = api($class, $method_name, $params , $httpMethod);

        $data = $errors = [];
        if($result->success){
            if(isset($result->data)){
                $data = $result->data;
            }else{
                if(isset($resultKey) && isset($result->{$resultKey})){
                    $data = $result->{$resultKey};
                }
                else{
                    $data = (object)[];
                }
            }
        }
        else{
            $errors = !empty($result->errors)? $result->errors : $result->error;
        }

        return [$data , $errors];
    }
}

