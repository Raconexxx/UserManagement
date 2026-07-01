# CI/CD

Dieser Ordner enthält Hinweise zur GitHub-Actions-Konfiguration.

Der aktive Workflow liegt technisch notwendig unter:

```text
.github/workflows/upload-files.yml
```

GitHub Actions führt Workflows nur aus, wenn sie unter `.github/workflows/` liegen.

## Deployment

Der Workflow lädt die Projektdateien per FTP hoch, sobald auf den Branch `NS` gepusht wird.

Benötigte GitHub Repository Variables:

- `FTP_URL`
- `FTP_USER`

Benötigte GitHub Repository Secrets:

- `FTP_PASSWORD`

Die Datenbankwerte `DB_DB`, `DB_USER` und `DB_PASSWORD` sind dokumentiert, werden im aktuellen Upload-Workflow aber noch nicht benötigt.
