<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Service Fee
    |--------------------------------------------------------------------------
    | Percentage (0–1) applied as a service fee on top of order subtotal.
    | Default: 0.05 (5%)
    */
    'service_fee' => env('TICKET_SERVICE_FEE', 0.05),
];
