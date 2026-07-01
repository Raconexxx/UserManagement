# UserManagement - Branch NS

Diese README beschreibt den aktuellen Stand für den Branch `NS`.

## Eingerichtete Zugänge

In GitHub sind Repository Variables und Secrets für FTP und Datenbank hinterlegt.

### Repository Variables

![GitHub Repository Variables](docs/images/github-variables.png)

Verwendete Variablen:

- `FTP_URL`
- `FTP_USER`
- `DB_DB`
- `DB_USER`

### Repository Secrets

![GitHub Repository Secrets](docs/images/github-secrets.png)

Verwendete Secrets:

- `FTP_PASSWORD`
- `DB_PASSWORD`

## Deployment

Der Workflow `.github/workflows/upload-files.yml` lädt die Dateien per FTP hoch.

Auslöser:

- Push auf den Branch `NS`
- manueller Start über `workflow_dispatch`

Für den FTP-Upload werden diese GitHub-Werte verwendet:

- Server: `${{ vars.FTP_URL }}`
- Benutzer: `${{ vars.FTP_USER }}`
- Passwort: `${{ secrets.FTP_PASSWORD }}`

## Ordnerstruktur

```text
.
|-- .github/workflows/upload-files.yml
|-- cicd/README.md
|-- docs/images/
|   |-- github-secrets.png
|   `-- github-variables.png
|-- README.md
`-- README_NS.md
```
