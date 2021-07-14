<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Application Supported Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the possible locales that can be used.
    | You are free to fill this array with any of the locales which will be
    | supported by the application.
    |
    |
    */
    'supported_locales' => [
        'en',
    ],

    /*
    |--------------------------------------------------------------------------
    | Render engine on save
    |--------------------------------------------------------------------------
    |
    | For now our package only provides one method of saving the content: html
    | Only edit the is you changed your html render engine key in mail-editor.php
    |
    |
    */
    'render_engine' => 'mail',
];
