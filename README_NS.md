# UserManagement - Branch NS

Diese README beschreibt den aktuellen Stand für den Branch `NS`.

## Eingerichtete Zugänge

In GitHub sind Repository Variables und Secrets für FTP und Datenbank hinterlegt.

### Repository Variables

![GitHub Repository Variables](docs/images/github-variables.png)

Verwendete Variablen:

- `FTP_URL`
- `FTP_USER`
- `DB_HOST` optional, wenn der Datenbankhost von `FTP_URL` abweicht
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

Vor dem Upload prüft der Workflow:

- FTP-Verbindung mit `FTP_URL`, `FTP_USER` und `FTP_PASSWORD`

Optional kann `FTP_TARGET_DIR` als Repository Variable gesetzt werden, wenn der Upload nicht direkt in das FTP-Root gehen soll.

Hochgeladen wird nur der Inhalt aus `web/`.

### Datenbank-Konfiguration

Die echte Datei `web/config/config.php` wird im GitHub-Actions-Lauf aus GitHub Variables und Secrets erzeugt und anschließend mit hochgeladen. Sie wird nicht im Repository gespeichert.

Benötigte Werte:

- `DB_DB`
- `DB_USER`
- `DB_PASSWORD`

Optional:

- `DB_HOST`, Standard ist `localhost`

Für lokale Entwicklung gibt es nur die Vorlage:

- `web/config/config.example.php`

## Git-Arbeit mit `NS`

Der Branch `NS` ist der Arbeitsbranch für diese Variante. Änderungen auf `NS` lösen den Upload-Workflow aus.

### Aktuellen Stand prüfen

```powershell
git status --short --branch
```

### `NS` mit `master` aktualisieren

Wenn neue Änderungen aus `master` übernommen werden sollen:

```powershell
git checkout NS
git fetch origin
git merge origin/master
```

Wenn Konflikte entstehen, müssen die betroffenen Dateien aufgelöst werden. Danach:

```powershell
git add .
git commit
git push origin NS
```

### Bei README-Konflikt die Version aus `master` nehmen

Wenn beim Merge von `origin/master` ein Konflikt in `README.md` entsteht und die Version aus `master` übernommen werden soll:

```powershell
git checkout --theirs README.md
git add README.md
git commit --no-edit
git push origin NS
```

Wichtig: `--theirs` passt in diesem Fall, weil `origin/master` in den aktuellen Branch `NS` hineingemergt wird.

### Nach `git pull origin NS` die eigene README-Version behalten

Wenn nach `git pull origin NS` wieder ein Konflikt in `README.md` entsteht und die bereits lokale Version behalten werden soll:

```powershell
git checkout --ours README.md
git add README.md
git commit --no-edit
git push origin NS
```

### Änderungen von `NS` nach `master` bringen

Empfohlen ist ein Pull Request auf GitHub:

```text
base: master
compare: NS
```

Direkt per Terminal geht es auch:

```powershell
git checkout master
git pull origin master
git merge NS
git push origin master
```

Der Pull Request ist sicherer, weil Änderungen vorher sichtbar geprüft werden können.

## Ordnerstruktur

```text
.
|-- .github/workflows/upload-files.yml
|-- cicd/README.md
|-- cicd/build-config.php
|-- cicd/deploy-ftp.sh
|-- cicd/test-ftp-connection.sh
|-- docs/images/
|   |-- github-secrets.png
|   `-- github-variables.png
|-- web/
|   |-- config/
|   |   |-- config.example.php
|   |   `-- style.css
|   `-- index.php
|-- README.md
`-- README_NS.md
```
