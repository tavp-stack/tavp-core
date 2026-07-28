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
];
