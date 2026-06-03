<?php

/*
|--------------------------------------------------------------------------
| Legal deadline rules
|--------------------------------------------------------------------------
| Durations (in days) per jurisdiction and deadline type. These are starting
| defaults and MUST be reviewed by a qualified lawyer before relying on them —
| statutory periods change and vary by case specifics. Editable here without
| touching code.
|
| jurisdiction: 'eg' = Egypt, 'sa' = Saudi Arabia
*/

return [

    'default_offsets' => [30, 14, 7, 3, 1], // alert this many days before due_date

    'rules' => [
        'eg' => [
            'appeal'         => 40, // استئناف مدني
            'cassation'      => 60, // نقض
            'objection'      => 10, // معارضة (جنائي)
            'grievance'      => 15, // تظلم
            'reconsideration'=> 30, // التماس إعادة نظر
        ],
        'sa' => [
            'appeal'         => 30, // استئناف
            'cassation'      => 30, // نقض
            'objection'      => 30, // اعتراض
            'grievance'      => 30, // تظلم
            'reconsideration'=> 30, // التماس إعادة نظر
        ],
    ],

    // Fallback when a (jurisdiction, type) pair has no explicit rule.
    'fallback_days' => 30,
];
