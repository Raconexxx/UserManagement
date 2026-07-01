<?php
$branch = 'NS';
$timestamp = date('Y-m-d H:i:s');
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>UserManagement Test</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            font-family: Arial, sans-serif;
            color: #1f2933;
            background: #f4f7fb;
        }

        main {
            width: min(560px, calc(100% - 32px));
            padding: 32px;
            border: 1px solid #d9e2ec;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
        }

        h1 {
            margin: 0 0 12px;
            font-size: 28px;
        }

        p {
            margin: 8px 0;
            line-height: 1.5;
        }

        code {
            padding: 2px 6px;
            border-radius: 4px;
            background: #edf2f7;
        }
    </style>
</head>
<body>
    <main>
        <h1>UserManagement Upload-Test</h1>
        <p>Wenn du diese Seite auf dem Server siehst, hat der GitHub-Actions-Upload funktioniert.</p>
        <p>Branch: <code><?= htmlspecialchars($branch, ENT_QUOTES, 'UTF-8') ?></code></p>
        <p>Serverzeit: <code><?= htmlspecialchars($timestamp, ENT_QUOTES, 'UTF-8') ?></code></p>
    </main>
</body>
</html>
