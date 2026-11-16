<?php

return [
    'season_start' => '2026-12-01',
    'season_end_exclusive' => '2027-05-01',

    'periods' => [
        'december-january' => ['label' => 'Декабрь–январь', 'opens_on' => '2026-12-01'],
        'january-february' => ['label' => 'Январь–февраль', 'opens_on' => '2027-01-10'],
        'february-march' => ['label' => 'Февраль–март', 'opens_on' => '2027-02-01'],
        'march-april' => ['label' => 'Март–апрель', 'opens_on' => '2027-03-01'],
    ],

    'previous_periods' => [
        'february' => ['label' => 'Февраль', 'opens_on' => '2026-02-01'],
        'march' => ['label' => 'Март', 'opens_on' => '2026-03-01'],
        'april' => ['label' => 'Апрель', 'opens_on' => '2026-04-01'],
        'may' => ['label' => 'Май', 'opens_on' => '2026-05-01'],
    ],
];
