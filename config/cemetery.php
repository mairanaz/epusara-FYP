<?php

return [
    'name' => env('CEMETERY_NAME', 'Tanah Perkuburan Islam Kg RTB Bukit Changgang'),

    'address' => env(
        'CEMETERY_ADDRESS',
        'Kampung RTB Bukit Changgang, Banting, Selangor'
    ),

    'latitude' => env('CEMETERY_LATITUDE'),

    'longitude' => env('CEMETERY_LONGITUDE'),

    'entrance_note' => env(
        'CEMETERY_ENTRANCE_NOTE',
        'Sila gunakan pintu masuk utama tanah perkuburan sebelum merujuk pelan lot kubur.'
    ),
];