<?php

declare(strict_types=1);

$required = [
    'DB_DB',
    'DB_USER',
    'DB_PASSWORD',
];

$missing = [];

foreach ($required as $name) {
    if (getenv($name) === false || getenv($name) === '') {
        $missing[] = $name;
    }
}

if ($missing !== []) {
    fwrite(STDERR, 'Fehlende Datenbankwerte: ' . implode(', ', $missing) . PHP_EOL);
    exit(1);
}

$config = <<<'PHP'
<?php

declare(strict_types=1);

const DB_HOST = %s;
const DB_NAME = %s;
const DB_USER = %s;
const DB_PASSWORD = %s;
const DB_CHARSET = 'utf8mb4';
const APP_BRANCH = %s;

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf('mysql:host=%%s;dbname=%%s;charset=%%s', DB_HOST, DB_NAME, DB_CHARSET);

    $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}
PHP;

$output = sprintf(
    $config,
    var_export((string) (getenv('DB_HOST') ?: 'localhost'), true),
    var_export((string) getenv('DB_DB'), true),
    var_export((string) getenv('DB_USER'), true),
    var_export((string) getenv('DB_PASSWORD'), true),
    var_export((string) (getenv('APP_BRANCH') ?: 'unknown'), true)
);

$target = __DIR__ . '/../web/config/config.php';

if (!is_dir(dirname($target))) {
    mkdir(dirname($target), 0775, true);
}

file_put_contents($target, $output);
