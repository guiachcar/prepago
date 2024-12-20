<?php

return [
    'certificates' => [
        'primary' => env('CERTIFICATE_SERVICE_PRIMARY', 'directdata'),
        'fallback' => env('CERTIFICATE_SERVICE_FALLBACK', 'infosimples'),
    ],

    'directdata' => [
        'token' => env('DIRECTDATA_TOKEN'),
    ],

    'infosimples' => [
        'token' => env('INFOSIMPLES_TOKEN'),
    ],
];
