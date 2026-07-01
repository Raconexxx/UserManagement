<?php

declare(strict_types=1);

session_start();

// Configuration loading
$configFile = __DIR__ . '/config/config.php';
if (!is_file($configFile)) {
    $configFile = __DIR__ . '/config/config.example.php';
}

require $configFile;
require __DIR__ . '/config/layout.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $username = trim((string) ($_POST['username'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $password_confirm = (string) ($_POST['password_confirm'] ?? '');

        // 1. Basic Validation
        if (empty($username) || empty($email) || empty($password)) {
            throw new Exception('Bitte alle Pflichtfelder ausfüllen.');
        }

        if ($password !== $password_confirm) {
            throw new Exception('Die Passwörter stimmen nicht überein.');
        }

        if (strlen($password) < 8) {
            throw new Exception('Das Passwort muss mindestens 8 Zeichen lang sein.');
        }

        // 2. Check if User or Email already exists
        $checkQuery = db()->prepare(
            'SELECT u.id, e.id as email_id 
             FROM app_users u
             LEFT JOIN app_users_emails e ON e.users_id = u.id
             WHERE u.username = :username OR e.email = :email'
        );
        $checkQuery->execute([
            'username' => $username,
            'email' => $email
        ]);
        $existing = $checkQuery->fetch();

        if ($existing) {
            if ($existing['id'] !== false) {
                throw new Exception('Benutzername ist bereits vergeben.');
            }
            if ($existing['email_id'] !== null) {
                throw new Exception('E-Mail-Adresse ist bereits vergeben.');
            }
        }

        // 3. Start Transaction
        db()->beginTransaction();

        // 4. Create User
        $insertUser = db()->prepare(
            'INSERT INTO app_users (username, password_hash) VALUES (:username, :password_hash)'
        );
        $insertUser->execute([
            'username' => $username,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);
        $userId = (int) db()->lastInsertId();

        // 5. Create Primary Email
        $insertEmail = db()->prepare(
            'INSERT INTO app_users_emails (users_id, email, is_primary, login_enabled, verified_at)
             VALUES (:users_id, :email, 1, 1, CURRENT_TIMESTAMP)'
        );
        $insertEmail->execute([
            'users_id' => $userId,
            'email' => $email
        ]);

        db()->commit();
        
        $message = 'Registrierung erfolgreich! Bitte loggen Sie sich ein.';
        // Optional: Redirect to login after success
        // header("Location: login_ns.php?registered=1");
        // exit;

    } catch (Throwable $exception) {
        if (db()->inTransaction()) {
            db()->rollBack();
        }
        $error = $exception->getMessage();
    }
}
?>
<?php head('Registrierung NS'); ?>
    <main>
        <h1>Registrierung NS</h1>

        <?php if ($message !== ''): ?>
            <p><strong style="color: green;"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></strong></p>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <p><strong style="color: red;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></strong></p>
        <?php endif; ?>

        <form method="post">
            <label for="username">Benutzername</label>
            <input id="username" name="username" type="text" value="<?= htmlspecialchars($username ?? '', ENT_QUOTES, 'UTF-8') ?>" required autocomplete="username">

            <label for="email">E-Mail-Adresse</label>
            <input id="email" name="email" type="email" value="<?= htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8') ?>" required autocomplete="email">

            <label for="password">Passwort</label>
            <input id="password" name="password" type="password" required autocomplete="new-password">

            <label for="password_confirm">Passwort wiederholen</label>
            <input id="password_confirm" name="password_confirm" type="password" required autocomplete="new-password">

            <button type="submit">Registrieren</button>
        </form>

        <p><a href="login_ns.php">Zur Login-Seite</a></p>
    </main>
<?php foot(); ?>