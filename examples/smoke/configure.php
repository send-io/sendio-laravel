<?php

// Adds the "sendio" mailer to config/mail.php and writes the .env values the smoke test needs.
// SENDIO_API_KEY is not written: it comes from the process environment.

$mail = file_get_contents('config/mail.php');
if (! str_contains($mail, "'transport' => 'sendio'")) {
    $mail = preg_replace(
        "/('mailers'\\s*=>\\s*\\[)/",
        "$1\n\n        'sendio' => [\n            'transport' => 'sendio',\n        ],\n",
        $mail,
        1
    );
    file_put_contents('config/mail.php', $mail);
}

$values = [
    'MAIL_MAILER' => 'sendio',
    'MAIL_FROM_ADDRESS' => getenv('SMOKE_FROM'),
    'MAIL_FROM_NAME' => '""',
    'SENDIO_BASE_URL' => getenv('SENDIO_BASE_URL') ?: '',
];

$lines = array_filter(
    explode("\n", file_get_contents('.env')),
    fn ($line) => ! in_array(strtok($line, '='), array_keys($values), true)
);
foreach ($values as $key => $value) {
    $lines[] = "{$key}={$value}";
}
file_put_contents('.env', implode("\n", $lines) . "\n");

echo "configured: MAIL_MAILER=sendio, sendio mailer registered\n";
