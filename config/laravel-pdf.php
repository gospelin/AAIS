<?php

return [
    'driver' => 'wkhtmltopdf',
    'drivers' => [
        'wkhtmltopdf' => [
            'binary' => public_path('bin/wkhtmltopdf'), // /home/auntyan1/public_html/AAIS/public/bin/wkhtmltopdf
            'options' => [
                'page-size' => 'A4',
                'margin-top' => 9,
                'margin-right' => 9,
                'margin-bottom' => 9,
                'margin-left' => 9,
                'encoding' => 'UTF-8',
                'no-outline' => true,
                'print-media-type' => true,
                'disable-smart-shrinking' => true,
                'enable-local-file-access' => true,
            ],
        ],
    ],
];
