<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'clip' => [
        'base_url' => env('CLIP_BASE_URL', 'https://api.payclip.com'),
        'public_key' => env('CLIP_PUBLIC_KEY'),
        'secret_key' => env('CLIP_SECRET_KEY'),
        'api_key' => env('CLIP_API_KEY'),
        'auth_scheme' => env('CLIP_AUTH_SCHEME', 'Basic'),
        'webhook_secret' => env('CLIP_WEBHOOK_SECRET'),
        // Solo para pruebas locales: acepta webhooks sin firma. NUNCA en produccion.
        'allow_unsigned_webhook' => env('CLIP_ALLOW_UNSIGNED_WEBHOOK', false),
        'webhook_url' => env('CLIP_WEBHOOK_URL'),
        'success_url' => env('CLIP_SUCCESS_URL'),
        'error_url' => env('CLIP_ERROR_URL'),
    ],

    'store' => [
        'shipping_cost' => (float) env('STORE_SHIPPING_COST', 150),
        'free_shipping_from' => (float) env('STORE_FREE_SHIPPING_FROM', 999),
        'low_stock_threshold' => (int) env('STORE_LOW_STOCK_THRESHOLD', 5),
        'max_upload_kb' => (int) env('STORE_MAX_UPLOAD_KB', 2048),
        'whatsapp' => env('STORE_WHATSAPP'),
        'theme' => env('STORE_THEME', 'volt'),
        'header_show_title' => filter_var(env('STORE_HEADER_SHOW_TITLE', false), FILTER_VALIDATE_BOOL),
        'meta_description' => env('STORE_META_DESCRIPTION', 'Tienda fitness de proteinas, suplementos y ropa deportiva.'),
        'maintenance_mode' => filter_var(env('STORE_MAINTENANCE_MODE', false), FILTER_VALIDATE_BOOL),
        'hero_carousel_slugs' => array_values(array_filter(array_map('trim', explode(',', env('STORE_HERO_CAROUSEL_SLUGS', 'combo-entrenamiento,super-pack,mega-combo'))))),
        'product_carousel_slugs' => array_values(array_filter(array_map('trim', explode(',', env('STORE_PRODUCT_CAROUSEL_SLUGS', 'combo-entrenamiento,super-pack,paquete-completo,oferta-flash-amino-inlabs-5-piezas,super-combo,combo-completo-azul'))))),
    ],

    'marketing' => [
        'meta_enabled' => filter_var(env('META_ADS_ENABLED', false), FILTER_VALIDATE_BOOL),
        'meta_pixel_id' => env('META_PIXEL_ID'),
        'meta_capi_access_token' => env('META_CAPI_ACCESS_TOKEN'),
        'meta_test_event_code' => env('META_TEST_EVENT_CODE'),

        'google_search_enabled' => filter_var(env('GOOGLE_SEARCH_ENABLED', false), FILTER_VALIDATE_BOOL),
        'google_site_verification' => env('GOOGLE_SITE_VERIFICATION'),
        'google_ads_enabled' => filter_var(env('GOOGLE_ADS_ENABLED', false), FILTER_VALIDATE_BOOL),
        'google_tag_id' => env('GOOGLE_TAG_ID'),
        'google_ads_conversion_id' => env('GOOGLE_ADS_CONVERSION_ID'),
        'google_ads_conversion_label' => env('GOOGLE_ADS_CONVERSION_LABEL'),
    ],

    'bank_transfer' => [
        'bank_name' => env('BANK_NAME'),
        'account_holder' => env('BANK_ACCOUNT_HOLDER'),
        'account_number' => env('BANK_ACCOUNT_NUMBER'),
        'clabe' => env('BANK_CLABE'),
        'instructions' => env('BANK_TRANSFER_INSTRUCTIONS'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
