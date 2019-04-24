<?php

if (!function_exists('api')) {
    /**
     * Helper function to get data from own API.
     *
     * @desc api(Api\UserController::class, 'resetPassword')
     *
     * @param string $class
     * @param string $methodName
     * @param array $params
     * @param string $httpMethod
     *
     * @return array
     */
    function api($class, $methodName, $params = true, $httpMethod = 'POST') {
        if ($params === true) {
            $params = request()->all();
        }

        $request = Request::create('', $httpMethod, $params);

        $api = app()->make($class);
        if (method_exists($api, $methodName)) {
            return $api->{$methodName}($request)->getData();
        }

        logger()->error('Method '. $methodName .' does not exist on class '. $class);
        return (object) ['success' => false, 'status' => 500];
    }
}

if (!function_exists('api_result')) {
    /**
     * Helper function to get data from own API and format the result.
     *
     * @desc api(Api\UserController::class, 'resetPassword')
     *
     * @param string $class
     * @param string $methodName
     * @param array $params
     * @param string $resultKey
     * @param string $httpMethod
     *
     * @return array
     */
    function api_result($class, $methodName, $params = true, $resultKey = null, $httpMethod = 'POST') {
        $result = api($class, $methodName, $params, $httpMethod);

        $data = [];
        $errors = [];

        if ($result->success) {
            if (isset($result->data)) {
                $data = $result->data;
            } else {
                if (isset($resultKey) && isset($result->{$resultKey})) {
                    $data = $result->{$resultKey};
                } else {
                    $data = (object) [];
                }
            }
        } else {
            $errors = !empty($result->errors) ? $result->errors : (!empty($result->error) ? $result->error : []);
        }

        return [$data, $errors];
    }
}
