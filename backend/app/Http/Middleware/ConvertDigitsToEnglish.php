<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ConvertDigitsToEnglish
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the input data from the request
        $input = $request->all();

        // Convert digits to English numbers in all input fields
        $convertedInput = $this->convertDigitsToEnglish($input);

        // Replace the original input with the converted one
        $request->replace($convertedInput);

        // Continue with the request
        return $next($request);
    }

    private function convertDigitsToEnglish($data)
    {
        // Recursive function to convert digits in arrays
        return array_map(function ($value) {
            if (is_array($value)) {
                return $this->convertDigitsToEnglish($value);
            }

            // Convert digits to English numbers
            return convertDigits($value);
        }, $data);
    }
}
