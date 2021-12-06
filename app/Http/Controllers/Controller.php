<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Return json response with status = true 
     *
     * @param string $message
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendSuccess(string $message, $data = null)
    {
        return $this->send(true, $message, $data);
    }

    /**
     * Return json response with status = false 
     *
     * @param string $message
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendError(string $message, $data = null)
    {
        return $this->send(false, $message, $data, 400);
    }

    public function sendWithCode(bool $status, string $message, int $responseCode, $data = null)
    {
        return $this->send($status, $message, $data, $responseCode);
    }


    private function send(bool $status, string $message, $data, int $statusCode = 200)
    {
        return response()->json([
            'status'   => $status,
            'message'   => $message,
            'payload'      => $data
        ], $statusCode);
    }
}
