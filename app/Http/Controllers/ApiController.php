<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    const ERROR_GENERAL = 'custom.general';

    /**
     * Return error response
     *
     * @return json - response data
     */
    public static function errorResponse($message = null, $errors = [], $code = 500, $type = self::ERROR_GENERAL)
    {
        $resposeData = [
            'success'   => false,
            'status'    => $code,
            'errors'    => $errors,
            'error'     => [
                'type'      => __($type),
                'message'   => $message,
            ]
        ];

        return new JsonResponse($resposeData, $code);
    }

    /**
     * Return success response
     *
     * @return json - response data
     */
    public static function successResponse($data = [], $dataMerge = false)
    {
        $response = ['success' => true];

        if (!empty($data)) {
            if ($dataMerge) {
                $response = array_merge($response, $data);
            } else {
                $response['data'] = $data;
            }
        }

        return new JsonResponse($response, 200);
    }
}
