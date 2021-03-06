<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    const ERROR_GENERAL = 'custom.general';
    const ERROR_MISSING = 'custom.unknown_error';
    const ERROR_FORBIDDEN = 'custom.forbidden_error';

    /**
     * Handle 404 errors
     *
     * @return json
     */
    public function handleMissingRoutes(Request $request)
    {
        return $this->errorResponse(null, [], 404, self::ERROR_MISSING);
    }

    /**
     * Handle 403 errors
     *
     * @return json
     */
    public function handleForbiddenRoutes(Request $request)
    {
        return $this->errorResponse(null, [], 403, self::ERROR_FORBIDDEN);
    }

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
    public static function successResponse($data = [], $dataMerge = false, $rsOptions = 0)
    {
        $response = ['success' => true];

        if (!empty($data)) {
            if ($dataMerge) {
                $response = array_merge($response, $data);
            } else {
                $response['data'] = $data;
            }
        }

        return new JsonResponse($response, 200, [], $rsOptions);
    }
}
