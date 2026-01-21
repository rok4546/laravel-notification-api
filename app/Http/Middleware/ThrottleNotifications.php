<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ThrottleNotifications
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check for the custom X-Client-Key header
        if (!$request->hasHeader('X-Client-Key')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Missing X-Client-Key header.',
            ], 401);
        }

        // Validate the header value (you can customize this)
        $clientKey = $request->header('X-Client-Key');
        $validKey = config('app.client_key', 'your-secret-key');

        if ($clientKey !== $validKey) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Invalid X-Client-Key.',
            ], 401);
        }

        return $next($request);
    }
}
