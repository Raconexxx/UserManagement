# CI/CD

Dieser Ordner enthält Hinweise zur GitHub-Actions-Konfiguration.

Der aktive Workflow liegt technisch notwendig unter:

```text
.github/workflows/upload-files.yml
```

GitHub Actions führt Workflows nur aus, wenn sie unter `.github/workflows/` liegen.

## Deployment

Der Workflow lädt die Projektdateien per FTP hoch, sobald auf den Branch `NS` gepusht wird.

Vor dem Upload laufen zwei Verbindungstests:

- `cicd/test-ftp-connection.sh`
- `cicd/test-db-connection.sh`

Benötigte GitHub Repository Variables:

- `FTP_URL`
- `FTP_USER`
- `DB_HOST` optional, wenn der Datenbankhost nicht identisch mit `FTP_URL` ist
- `DB_DB`
- `DB_USER`

Benötigte GitHub Repository Secrets:

- `FTP_PASSWORD`
- `DB_PASSWORD`

Der Datenbanktest nutzt `DB_HOST`, falls die Variable existiert. Wenn `DB_HOST` nicht gesetzt ist, verwendet er `FTP_URL` als Fallback.
