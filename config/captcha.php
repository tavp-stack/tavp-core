<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Captcha Type
    |--------------------------------------------------------------------------
    |
    | Supported: 'math', 'puzzle', 'slider'
    |
    | Admin can change this via CMS admin panel.
    |
    */
    'type' => env('CAPTCHA_TYPE', 'math'),

    /*
    |--------------------------------------------------------------------------
    | Math Captcha Options
    |--------------------------------------------------------------------------
    |
    | Keep the default gentle so everyone can solve it. Operators and range
    | can be tightened per deployment if needed.
    |
    */
    'math' => [
        'max' => (int) env('CAPTCHA_MATH_MAX', 10),
        'allow_minus' => (bool) env('CAPTCHA_MATH_MINUS', false),
        'allow_multiply' => (bool) env('CAPTCHA_MATH_MULTIPLY', false),
    ],
];
