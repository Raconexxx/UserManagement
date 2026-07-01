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

Benötigte GitHub Repository Variables:

- `FTP_URL`
- `FTP_USER`
- `FTP_TARGET_DIR` optional, wenn nicht direkt in das FTP-Root hochgeladen werden soll

Benötigte GitHub Repository Secrets:

- `FTP_PASSWORD`

Wenn der Upload erfolgreich läuft, aber im Webordner nichts sichtbar ist, muss wahrscheinlich `FTP_TARGET_DIR` auf den richtigen Zielordner beim Hoster gesetzt werden.
