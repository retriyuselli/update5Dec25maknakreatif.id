<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Invoice Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for invoice generation,
    | including company information and formatting options.
    |
    */

    // Company Information
    'company_name' => env('INVOICE_COMPANY_NAME', config('app.name')),
    'address' => env('INVOICE_ADDRESS', 'Jln. Sintraman Jaya, No. 2148, Sekip Jaya, Palembang'),
    'phone' => env('INVOICE_PHONE', '+62 813 731 83794'),
    'email' => env('INVOICE_EMAIL', 'maknawedding@gmail.com'),
    'website' => env('INVOICE_WEBSITE', 'www.paketpernikahan.co.id'),
    // 'tax_id' => env('INVOICE_TAX_ID', '123-456-789'),

    // Invoice Branding
    'logo' => env('INVOICE_LOGO', 'images/logomkiinv.png'),
    'primary_color' => env('INVOICE_PRIMARY_COLOR', '#2d3748'),
    'secondary_color' => env('INVOICE_SECONDARY_COLOR', '#4a5568'),

    // Payment Information
    'payment_days' => env('INVOICE_PAYMENT_DAYS', 7),
    'currency' => env('INVOICE_CURRENCY', 'IDR'),
    'currency_symbol' => env('INVOICE_CURRENCY_SYMBOL', 'Rp'),

    // Terms and Conditions
    'terms' => [
        'Payment is due within the specified due date',
        'Please make payments via bank transfer to the account provided',
        'Late payments may be subject to additional fees',
        'For questions regarding this invoice, please contact our finance department',
    ],

    // Bank Information
    'bank_accounts' => [
        [
            'bank_name' => env('INVOICE_BANK_NAME', 'Bank BCA'),
            'account_name' => env('INVOICE_ACCOUNT_NAME', 'Your Company Name'),
            'account_number' => env('INVOICE_ACCOUNT_NUMBER', '123-4567-890'),
        ],
    ],

    // PDF Settings
    'paper_size' => env('INVOICE_PAPER_SIZE', 'a4'),
    'paper_orientation' => env('INVOICE_PAPER_ORIENTATION', 'portrait'),
];
