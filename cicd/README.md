# CI/CD

Dieser Ordner enthält Hinweise zur GitHub-Actions-Konfiguration.

Der aktive Workflow liegt technisch notwendig unter:

```text
.github/workflows/upload-files.yml
```

GitHub Actions führt Workflows nur aus, wenn sie unter `.github/workflows/` liegen.

## Deployment

Der Workflow lädt die Projektdateien per FTP hoch, sobald auf den Branch `NS` gepusht wird.

Vor dem Upload läuft ein FTP-Verbindungstest:

- `cicd/test-ftp-connection.sh`

Der Upload selbst läuft über `lftp`:

- `cicd/deploy-ftp.sh`

Vor dem Upload wird die Datenbank-Konfiguration erzeugt:

- `cicd/build-config.php`

Hochgeladen wird nur der Inhalt aus `web/`.

Benötigte GitHub Repository Variables:

- `FTP_URL`
- `FTP_USER`
- `FTP_TARGET_DIR` optional, wenn nicht direkt in das FTP-Root hochgeladen werden soll
- `DB_DB`
- `DB_USER`
- `DB_HOST` optional, Standard ist `localhost`

Benötigte GitHub Repository Secrets:

- `FTP_PASSWORD`
- `DB_PASSWORD`

Wenn der Upload erfolgreich läuft, aber im Webordner nichts sichtbar ist, muss wahrscheinlich `FTP_TARGET_DIR` auf den richtigen Zielordner beim Hoster gesetzt werden.

Der Upload deaktiviert EPSV (`ftp:prefer-epsv no`), weil einige FTP-Server den Datenkanal sonst mit `ECONNRESET` abbrechen.

Die Datei `web/config/config.php` wird aus den GitHub-Werten generiert und nicht im Repository gespeichert.
