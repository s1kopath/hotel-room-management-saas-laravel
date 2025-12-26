<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

/**
 * @OA\Info(
 *    title="API Documentation",
 *    description="API Documentation",
 *    version="1.0.0",
 * )
 */

abstract class Controller
{
    /**
     * Send a standardized successful JSON response.
     *
     * @param mixed       $data    Optional payload data to return (e.g., model, array, resource).
     * @param string      $message A success message describing the operation.
     * @param int         $code    HTTP status code (default is 200 OK).
     * @return JsonResponse
     */
    public function sendResponse(mixed $data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        // Build the success response array, omitting null fields with array_filter
        $response = array_filter([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ]);

        // Return as a JSON response with the given HTTP status code
        return response()->json($response, $code);
    }

    /**
     * Send a standardized error JSON response.
     *
     * @param string            $message A message describing the error.
     * @param array|string|null $errors  Additional error details (e.g., validation messages).
     * @param int               $code    HTTP status code (default is 400 Bad Request).
     * @return JsonResponse
     */
    public function sendError(string $message = 'An error occurred', array|string|null $errors = null, int $code = 400): JsonResponse
    {
        // Build the error response array
        $response = [
            'success' => false,
            'message' => $message,
        ];

        // Add error details if provided (wrap string in an array)
        if ($errors !== null) {
            $response['errors'] = is_array($errors) ? $errors : ['error' => $errors];
        }

        // Return as a JSON response with the given HTTP error code
        return response()->json($response, $code);
    }
}
