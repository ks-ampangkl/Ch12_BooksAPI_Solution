<?php // src/Middleware/Cors.php
namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class Cors implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 1. Instantly return a clean 204 response for browser Preflight OPTIONS checks
        if ($request->getMethod() === 'OPTIONS') {
            $response = new \Slim\Psr7\Response(204);
            return $this->withCors($request,$response);
        }

        // 2. Process the request down the line
        $response = $handler->handle($request);
        
        // 3. Stamp the CORS headers on the resulting response on its way out
        return $this->withCors($request,$response);
    }
 
private array $allowed; 
  
public function __construct() { 
    $list = (string)($_ENV['CORS_ALLOWED_ORIGINS'] ?? ''); 
    $this->allowed = array_filter(array_map('trim', explode(',', $list))); 
} 
  
private function withCors($req, $res) { 
    $origin = $req->getHeaderLine('Origin'); 
    $allow  = '*'; $creds = false; 
    if ($this->allowed && in_array($origin, $this->allowed, true)) { 
        $allow = $origin; $creds = true; 
    } 
    $res = $res 
        ->withHeader('Access-Control-Allow-Origin',  $allow) 
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization') 
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS') 
        ->withHeader('Vary', 'Origin'); 
    if ($creds) $res = $res->withHeader('Access-Control-Allow-Credentials', 'true'); 
    return $res; 
} 
}