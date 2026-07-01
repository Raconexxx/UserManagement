<?php

declare(strict_types=1);

session_start();

// Die echte config.php wird im GitHub-Actions-Lauf aus Secrets erzeugt.
// Lokal fällt die Seite auf config.example.php zurück.
$configFile = __DIR__ . '/config/config.php';

if (!is_file($configFile)) {
    $configFile = __DIR__ . '/config/config.example.php';
}

require $configFile;

$login = trim((string) ($_POST['login'] ?? ''));
$password = (string) ($_POST['password'] ?? '');
$message = '';
$loggedInUser = $_SESSION['user'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    $_SESSION = [];
    session_destroy();
    $loggedInUser = null;
    $message = 'Logout erfolgreich.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ein Login ist per Benutzername oder per freigegebener E-Mail-Adresse möglich.
    $statement = db()->prepare(
        'SELECT DISTINCT u.id, u.username, u.password_hash
         FROM app_users u
         LEFT JOIN app_users_emails e
            ON e.users_id = u.id
            AND e.login_enabled = 1
         WHERE u.deactivated_at IS NULL
           AND (u.username = :login OR e.email = :login)
         LIMIT 1'
    );
    $statement->execute(['login' => $login]);
    $user = $statement->fetch();

    if ($user !== false && password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'username' => (string) $user['username'],
        ];

        $loggedInUser = $_SESSION['user'];
        $message = 'Login erfolgreich.';
    } else {
        // Absichtlich allgemein halten, damit nicht erkennbar ist, ob User oder Passwort falsch war.
        $message = 'Login fehlgeschlagen.';
    }
}
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login NS</title>
    <link rel="stylesheet" href="config/style.css">
</head>
<body>
    <main>
        <h1>Login NS</h1>
        <p>Testzugang: <code>testuserin</code> oder <code>testuserin@example.com</code> mit <code>Test123!</code></p>

        <?php if ($message !== ''): ?>
            <p><strong><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></strong></p>
        <?php endif; ?>

        <?php if ($loggedInUser !== null): ?>
            <section>
                <h2>Angemeldet</h2>
                <p>Benutzer: <code><?= htmlspecialchars($loggedInUser['username'], ENT_QUOTES, 'UTF-8') ?></code></p>
                <form method="post">
                    <button type="submit" name="logout" value="1">Abmelden</button>
                </form>
            </section>
        <?php else: ?>
            <form method="post">
                <label for="login">Benutzername oder E-Mail</label>
                <input id="login" name="login" type="text" autocomplete="username" required>

                <label for="password">Passwort</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required>

                <button type="submit">Anmelden</button>
            </form>
        <?php endif; ?>
    </main>
</body>
</html>
