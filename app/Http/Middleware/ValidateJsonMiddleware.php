<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateJsonMiddleware
{
    /**
     * Check if request has a valid JSON
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isJson()) {
            try {
                json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
            } catch (Exception) {
                abort(400, 'Malformed JSON');
            }
        } else {
            abort(415, 'Unsupported Media Type. Request must accept application/json');
        }

        // Proceed if everything is fine
        return $next($request);
    }
}
