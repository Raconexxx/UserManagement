<?php
$configFile = __DIR__ . '/config/config.php';

if (!is_file($configFile)) {
    $configFile = __DIR__ . '/config/config.example.php';
}

require $configFile;

$branch = defined('APP_BRANCH') ? APP_BRANCH : 'unbekannt';
$timestamp = date('Y-m-d H:i:s');
$dbStatus = 'nicht geprüft';
$tables = [];

try {
    $pdo = db();
    $pdo->query('SELECT 1');
    $dbStatus = 'verbunden';

    $tableStatement = $pdo->query('SHOW TABLES');
    $tables = $tableStatement->fetchAll(PDO::FETCH_COLUMN);
} catch (Throwable $exception) {
    $dbStatus = 'nicht verbunden';
}
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>UserManagement Test</title>
    <link rel="stylesheet" href="config/style.css">
</head>
<body>
    <main>
        <h1>UserManagement Upload-Test</h1>
        <p>Wenn du diese Seite auf dem Server siehst, hat der GitHub-Actions-Upload funktioniert.</p>
        <p>Branch: <code><?= htmlspecialchars($branch, ENT_QUOTES, 'UTF-8') ?></code></p>
        <p>Serverzeit: <code><?= htmlspecialchars($timestamp, ENT_QUOTES, 'UTF-8') ?></code></p>
        <p>Datenbank: <code><?= htmlspecialchars($dbStatus, ENT_QUOTES, 'UTF-8') ?></code></p>

        <section>
            <h2>Tabellen</h2>
            <?php if ($dbStatus !== 'verbunden'): ?>
                <p>Keine Tabellen abrufbar, weil die Datenbankverbindung nicht steht.</p>
            <?php elseif ($tables === []): ?>
                <p>Keine Tabellen vorhanden.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($tables as $table): ?>
                        <li><?= htmlspecialchars((string) $table, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
