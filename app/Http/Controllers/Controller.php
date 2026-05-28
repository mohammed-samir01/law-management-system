<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class Controller
{
    protected function apiResponse(
        mixed $data = null,
        string $message = '',
        bool $success = true,
        int $status = 200,
        ?LengthAwarePaginator $paginator = null
    ): JsonResponse {
        $response = [
            'success' => $success,
            'message' => $message,
            'data'    => $data,
            'meta'    => [],
        ];

        if ($paginator) {
            $response['meta']['pagination'] = [
                'total'        => $paginator->total(),
                'per_page'     => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
            ];
        }

        return response()->json($response, $status);
    }
}
