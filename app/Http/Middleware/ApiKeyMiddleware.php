<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {

        $apiKey = $request->header('x-api-key');

        if (!$apiKey) {
            return response()->json([
                'message' => 'API key is required.'
            ], 401);
        }

        if (!hash_equals(
            (string) config('services.api_auth.api_key'),
            (string) $apiKey
        )) {
            return response()->json([
                'message' => 'Invalid API key.'
            ], 401);
        }

        $companyApiKey = $request->header('x-company-api-key');

        if (!$companyApiKey) {
            return response()->json([
                'message' => 'Company API key is required.'
            ], 401);
        }

        $company = Company::where('api_key', $companyApiKey)->first();

        if (!$company) {
            return response()->json([
                'message' => 'Invalid company API key.'
            ], 401);
        }

        $request->attributes->set('company', $company);

        return $next($request);
    }
}