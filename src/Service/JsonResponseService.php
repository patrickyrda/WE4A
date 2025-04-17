<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class JsonResponseService
{
    //In case of sending data inside of Doctrine Collections, serialize it first
    public function success(array $data = [], string $message = 'Success', int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    public function error(string $message = 'An error occurred', array $errors = [], int $status = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return new JsonResponse([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}