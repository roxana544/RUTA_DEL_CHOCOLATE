<?php

return [
    // Distancia máxima (metros) entre el turista y la fábrica para validar el checkpoint por GPS.
    'radius_m' => (int) env('CHECKPOINT_RADIUS_M', 300),

    // true: exige GPS dentro del radio. false: acepta la foto sin GPS (útil para pruebas locales)
    // y la marca como "sin verificar".
    'strict' => (bool) env('CHECKPOINT_STRICT', true),

    'points' => 10,
    'unverified_points' => 5,
    'completion_bonus' => 50,
];
