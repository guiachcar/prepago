<?php

return [
    'services' => [
        'state-court' => [
            'primary' => 'directdata',
            'fallback' => 'infosimples',
            'method' => 'getStateCourt'
        ],
        'federal-court' => [
            'primary' => 'infosimples',
            'fallback' => 'directdata',
            'method' => 'getFederalCourt'
        ],
        'labour-court' => [
            'primary' => 'infosimples',
            'fallback' => 'directdata',
            'method' => 'getLabourCourt'
        ],
        'protests' => [
            'primary' => 'directdata',
            'fallback' => 'infosimples',
            'method' => 'getProtests'
        ],
        'receita-federal' => [
            'primary' => 'infosimples',
            'fallback' => 'directdata',
            'method' => 'getReceitaFederal'
        ],
        'debt-certificate' => [
            'primary' => 'infosimples',
            'fallback' => 'directdata',
            'method' => 'getDebtCertificate'
        ],
        'cndt' => [
            'primary' => 'infosimples',
            'fallback' => 'directdata',
            'method' => 'getCNDT'
        ]
    ]
];
