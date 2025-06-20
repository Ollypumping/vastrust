<?php
namespace App\Helpers;

class ResponseHelper
{
    public static function success($data = [], $message = "Success", $code = 200)
    {
        http_response_code($code);
        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ]);
    }

    public static function error($errors = [], $message = "An error occurred", $code = 400)
    {
        http_response_code($code);
        echo json_encode([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors
        ]);
    }
}