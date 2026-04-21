<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment Instructions (Email)
    |--------------------------------------------------------------------------
    |
    | This text is included in the payment schedule email sent to clients.
    | Use PAYMENT_INSTRUCTIONS in .env to customize it (multi-line supported).
    |
    */
    'instructions' => env('PAYMENT_INSTRUCTIONS', "Please pay your monthly installment on or before the due date.\n\nPayment options:\n- Cash (office)\n- Bank transfer\n- GCash\n\nUse your Payment Plan Number as reference."),
];
