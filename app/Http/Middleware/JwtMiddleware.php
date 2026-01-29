<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;

class JwtMiddleware
{
    public function handle($request, Closure $next)
    {
        try {
            $token = $request->bearerToken();
            
            if (!$token && $request->cookie('jwt_token')) {
                $token = $request->cookie('jwt_token');
            }

            if (!$token) {
                return response()->json(['error' => 'Token not provided'], Response::HTTP_UNAUTHORIZED);
            }

            JWTAuth::setToken($token);
            
            $user = JWTAuth::authenticate();
            
            if (!$user) {
                return response()->json(['error' => 'User not found'], Response::HTTP_UNAUTHORIZED);
            }

        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token has expired'], Response::HTTP_UNAUTHORIZED);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token is invalid'], Response::HTTP_UNAUTHORIZED);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not validate token'], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}