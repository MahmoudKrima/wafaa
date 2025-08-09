<?php

namespace App\Traits;

use Illuminate\Http\Exceptions\HttpResponseException;

trait ApiResponseTrait
{
    public static function apiResponse($data = [], $message = '', $error = [], $status = 200)
    {
        $response = response()
            ->json([
                'data' => $data,
                'message' => $message,
                'error' => $error,
                'status' => $status
            ], $status);
        $response->header('Cache-Control', 'public, max-age=3600');
        return $response;
    }

    public static function failedValidation($validator, $data = [], $message = '', $status)
    {
        $errors = $validator->errors()->toArray();
        $response = [
            'data' => $data,
            'message' => $message,
            'errors' => $errors,
            'status' => $status
        ];
        throw new HttpResponseException(response()->json($response, $status));
    }

    public static function customizedFailedValidation($validator, $data = [], $message = '', $status = 422)
    {
        $formattedErrors = [];
        $summaryMessages = [];
        foreach ($validator->errors()->messages() as $key => $messages) {
            $newKey = preg_replace('/[\.\*]/', '_', $key);
            $newKey = preg_replace('/_\d+/', '', $newKey);
            $newKey = rtrim($newKey, '_');
            foreach ($messages as $message) {
                $formattedMessage = preg_replace('/[\.\*]/', '', $message);
                $formattedMessage = preg_replace('/\d+/', '_', $formattedMessage);
                $formattedMessage = preg_replace('/\s+/', ' ', trim($formattedMessage));
                $formattedErrors[$newKey][0] = $formattedMessage;
                $summaryMessages[] = $formattedMessage;
            }
        }
        $errorCount = count($summaryMessages) - 1;
        $summaryMessage = !empty($summaryMessages) ? $summaryMessages[0] . " (and {$errorCount} more errors)" : '';
        $response = [
            'data' => $data,
            'message' =>  __('api.validation_error'),
            'errors' => $formattedErrors,
            'status' => $status
        ];
        throw new HttpResponseException(response()->json($response, $status));
    }
}
