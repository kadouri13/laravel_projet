<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Scientific Article Management API",
 *      description="API documentation for the Scientific Article Management system",
 *      @OA\Contact(
 *          email="admin@example.com"
 *      )
 * )
 *
 * @OA\Server(
 *      url="http://localhost:8000/api",
 *      description="Demo API Server"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
