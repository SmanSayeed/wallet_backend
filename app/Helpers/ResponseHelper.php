<?php
namespace App\Helpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ResponseHelper
{
    public static function success(string $message, $data = null, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'status_code'=>$statusCode
        ], $statusCode);
    }

    public static function error(string $message, $errors = null, int $statusCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $errors,
            'status_code'=>$statusCode
        ], $statusCode);
    }
}
