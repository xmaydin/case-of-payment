<?php

namespace App\Traits;


use Illuminate\Support\Facades\Response;

trait ResponseTrait
{

    /**
     * @param $message
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendError($message = null, array $data = [], $code = 404)
    {
        return Response::json([
            'status' => false,
            'result' => $data,
            'message' => $message
        ], $code);
    }

    /**
     * @param string|null $message
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResponse(string $message = null, array $data = [])
    {
        return Response::json([
            'status' => true,
            'result' => $data,
            'message' => $message
        ]);
    }

}
