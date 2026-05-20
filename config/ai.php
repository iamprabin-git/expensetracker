<?php

return [

    /*
    |--------------------------------------------------------------------------
    | AI receipt / bill scanning
    |--------------------------------------------------------------------------
    |
    | Supports Google Gemini (GEMINI_API_KEY) or OpenAI (OPENAI_API_KEY).
    | Set AI_SCAN_PROVIDER to "gemini", "openai", or "auto" (default: first key found).
    |
    */

    'gemini_api_key' => env('GEMINI_API_KEY'),

    'openai_api_key' => env('OPENAI_API_KEY'),

    'provider' => env('AI_SCAN_PROVIDER', 'auto'),

    'scan_model' => env('AI_SCAN_MODEL', 'gemini-2.5-flash'),

    'max_upload_kb' => (int) env('AI_SCAN_MAX_UPLOAD_KB', 5120),

];
