# UserManagement

Dieses Repository enthält die Dateien für das UserManagement-Projekt.

## Branch

Aktiver Arbeitsbranch: `NS`

Der CI/CD-Workflow ist ebenfalls auf den Branch `NS` begrenzt. Änderungen auf anderen Branches lösen kein Deployment aus.

## CI/CD

Das Deployment läuft über GitHub Actions und lädt die Projektdateien per FTP hoch.

Workflow-Datei:

- `.github/workflows/upload-files.yml`

Zusätzliche Hinweise:

- `cicd/README.md`

## GitHub-Konfiguration

Für den Workflow sind folgende Repository-Einstellungen notwendig:

### Variables

- `FTP_URL`
- `FTP_USER`
- `DB_HOST` optional, wenn der Datenbankhost von `FTP_URL` abweicht
- `DB_DB`
- `DB_USER`

### Secrets

- `FTP_PASSWORD`
- `DB_PASSWORD`

Die Werte werden nicht im Repository gespeichert.

## Dokumentation

Die Screenshots der eingerichteten GitHub-Zugänge liegen unter:

- `docs/images/github-variables.png`
- `docs/images/github-secrets.png`
