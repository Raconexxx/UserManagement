<?php

declare(strict_types=1);

header('Content-Type: text/html; charset=utf-8');

if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}

function head(string $title): void
{
    $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

    echo <<<HTML
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$safeTitle}</title>
    <link rel="stylesheet" href="config/style.css">
</head>
<body>
HTML;
}

function foot(): void
{
    echo <<<HTML
</body>
</html>
HTML;
}
