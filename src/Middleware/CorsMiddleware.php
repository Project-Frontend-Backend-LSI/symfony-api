<?php

namespace App\Middleware;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class CorsMiddleware implements HttpKernelInterface
{
    private $app;

    public function __construct(HttpKernelInterface $app)
    {
        $this->app = $app;
    }

    public function handle(Request $request, int $type = HttpKernelInterface::MASTER_REQUEST, bool $catch = true): Response
    {
        $response = $this->app->handle($request, $type, $catch);
        $response->headers->set('Access-Control-Allow-Origin', $_ENV['CORS_ALLOW_ORIGIN']);
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Max-Age', '3600');

        return $response;
    }
}
