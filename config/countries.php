<?php

return [
    'cache'      => [
        'enabled'  => true,
        'service'  => PragmaRX\Countries\Support\Cache::class,
        'duration' => 180,
    ],
    'hydrate'    => [
        'before' => true,

        'after' => true,

        'elements' => [
            'flag'       => true,
            'currency'   => false,
            'states'     => false,
            'timezone'   => false,
            'borders'    => false,
            'topology'   => false,
            'geometry'   => false,
            'collection' => false,
        ],
    ],
    'maps'       => [
        'lca3'     => 'ISO639_3',
        'currency' => 'ISO4217',
    ],
    'validation' => [
        'enabled' => true,
        'rules'   => [
            'country'        => 'name.common',
            'cca2',
            'cca2',
            'cca3',
            'ccn3',
            'cioc',
            'currency'       => 'ISO4217',
            'language',
            'language_short' => 'ISO639_3',
        ],
    ],
];
