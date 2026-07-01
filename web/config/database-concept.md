# Datenbank-Konzept

Die Struktur ist auf Benutzerverwaltung, Gruppen und flexible Berechtigungen ausgelegt.

## Tabellen

- `app_users`: Benutzer mit `active`, Zeitstempeln und optionalem `deactivated_at`
- `app_groups`: Benutzergruppen mit `active`, Zeitstempeln und optionalem `deactivated_at`
- `app_permissions`: einzelne Berechtigungen, zum Beispiel `users.read` oder `users.write`
- `app_user_groups`: Zuordnung Benutzer zu Gruppen
- `app_group_permissions`: Zuordnung Gruppen zu Berechtigungen
- `app_user_permissions`: direkte Benutzer-Berechtigungen als Ausnahme mit `allow` oder `deny`

## Löschen

Benutzer und Gruppen werden fachlich nicht hart gelöscht. Stattdessen wird `active` auf `0` gesetzt und bei Bedarf `deactivated_at` befüllt.

Beispiel:

```sql
UPDATE app_users
SET active = 0,
    deactivated_at = CURRENT_TIMESTAMP
WHERE id = 1;
```

## Normalisierung

Die Struktur trennt Benutzer, Gruppen und Berechtigungen voneinander. Mehrfachbeziehungen liegen in eigenen Verbindungstabellen. Dadurch bleiben die Daten flexibel und vermeiden Wiederholungen.
