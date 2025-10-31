<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiRequired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $private_api_key = "298124808085049058408mmxfdhkjafkhxakfhhfkakjf8498098209840858mnfkjwkjhf"; // in real life we store this in database

        $api_key = $request->headers->get('X-Api-Key');

        if ($api_key !== $private_api_key) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }else{
            return $next($request);
        }
    }
}
