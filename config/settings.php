<?php

// Should be set to 0 in production
error_reporting(E_ALL);

// Should be set to '0' in production
ini_set('display_errors', '1');

// Settings

// can use this log the info
$settings = [
    'logger' => [
        'path' => './logs/chatapi.log', // Set the log file path
        'level' => 'info', // Set the minimum log level (e.g., info, warning, error)
    ]
];

return $settings;