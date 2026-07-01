# Datenbank-Konzept

Die Struktur ist auf Benutzerverwaltung, Gruppen und flexible Berechtigungen ausgelegt.

## Tabellen

- `app_users`: Benutzer mit Zeitstempeln und optionalem `deactivated_at`
- `app_groups`: Benutzergruppen mit Zeitstempeln und optionalem `deactivated_at`
- `app_permissions`: einzelne Berechtigungen, zum Beispiel `users.read` oder `users.write`
- `app_user_groups`: Zuordnung Benutzer zu Gruppen
- `app_group_permissions`: Zuordnung Gruppen zu Berechtigungen
- `app_user_permissions`: direkte Benutzer-Berechtigungen als Ausnahme mit `allow` oder `deny`

## Löschen

Benutzer und Gruppen werden fachlich nicht hart gelöscht. Stattdessen wird `deactivated_at` befüllt. Ist `deactivated_at` leer, gilt der Datensatz als aktiv.

Beispiel:

```sql
UPDATE app_users
SET deactivated_at = CURRENT_TIMESTAMP
WHERE id = 1;
```

Reaktivieren:

```sql
UPDATE app_users
SET deactivated_at = NULL
WHERE id = 1;
```

Aktive Benutzer abfragen:

```sql
SELECT *
FROM app_users
WHERE deactivated_at IS NULL;
```

## Neuaufbau

In `web/config/bootstrap.php` steuert `BOOTSTRAP_DROP_TABLES`, ob die App-Tabellen vor dem Bootstrap gelöscht werden.

```php
const BOOTSTRAP_DROP_TABLES = 1;
```

`1` löscht und baut neu auf. `0` legt nur fehlende Tabellen an.

## Normalisierung

Die Struktur trennt Benutzer, Gruppen und Berechtigungen voneinander. Mehrfachbeziehungen liegen in eigenen Verbindungstabellen. Dadurch bleiben die Daten flexibel und vermeiden Wiederholungen.
