# Datenbank-Konzept

Die Struktur ist auf Benutzerverwaltung, Gruppen und flexible Berechtigungen ausgelegt.

## Tabellen

- `app_users`: Benutzerkonto mit Benutzername, Passwort-Hash, Zeitstempeln und optionalem `deactivated_at`
- `app_user_profiles`: optionale 1:1 Profildaten wie Vorname, Nachname, Anzeigename und Geburtsdatum
- `app_user_emails`: mehrere E-Mail-Adressen pro Benutzer, optional als Login freigegeben
- `app_user_addresses`: mehrere Adressen pro Benutzer, zum Beispiel privat, Arbeit oder Rechnung
- `app_contact_types`: Typen für flexible Kontaktdaten, zum Beispiel `mobile`, `phone`, `website` oder Messenger
- `app_user_contact_methods`: flexible Kontaktdaten pro Benutzer mit Verweis auf `app_contact_types`
- `app_user_attributes`: flexible Zusatzdaten als Schlüssel-Wert-Paare
- `app_groups`: Benutzergruppen mit Zeitstempeln und optionalem `deactivated_at`
- `app_permissions`: einzelne Berechtigungen, zum Beispiel `users.read` oder `users.write`
- `app_user_groups`: Zuordnung Benutzer zu Gruppen
- `app_group_permissions`: Zuordnung Gruppen zu Berechtigungen
- `app_user_permissions`: direkte Benutzer-Berechtigungen als Ausnahme mit `allow` oder `deny`

## Benutzer und E-Mail-Adressen

E-Mail-Adressen liegen bewusst nicht in `app_users`. Ein Benutzer kann mehrere E-Mail-Adressen haben. Jede E-Mail-Adresse kann einzeln verifiziert und für Login freigegeben werden.

Login über E-Mail-Adresse:

```sql
SELECT u.*
FROM app_users u
JOIN app_user_emails e ON e.user_id = u.id
WHERE e.email = 'name@example.com'
  AND e.login_enabled = 1
  AND u.deactivated_at IS NULL;
```

Primäre E-Mail-Adresse setzen:

```sql
UPDATE app_user_emails
SET is_primary = 0
WHERE user_id = 1;

UPDATE app_user_emails
SET is_primary = 1
WHERE user_id = 1
  AND id = 3;
```

## Adressen und flexible Daten

Adressen liegen in `app_user_addresses`, damit ein Benutzer mehrere Adressen haben kann.

Telefonnummern, Webseiten oder Messenger-Kontakte liegen in `app_user_contact_methods`. Dort kann ein Benutzer beliebig viele Werte pro Typ haben. Die Typen selbst liegen in `app_contact_types`, damit sie nicht als wiederholter Freitext gespeichert werden.

Beispiel für mehrere Kontaktdaten:

```sql
INSERT INTO app_contact_types (type_key, name)
VALUES
    ('mobile', 'Mobiltelefon'),
    ('phone', 'Telefon'),
    ('website', 'Webseite');

INSERT INTO app_user_contact_methods (user_id, contact_type_id, contact_value, label, is_primary)
VALUES
    (1, 1, '+491701234567', 'privat', 1),
    (1, 2, '+49301234567', 'Büro', 0),
    (1, 3, 'https://example.com', 'Portfolio', 0);
```

Für Felder, die nicht als Kontakt oder Adresse modelliert sind, gibt es `app_user_attributes`.

Beispiel für flexible Zusatzdaten:

```sql
INSERT INTO app_user_attributes (user_id, attribute_key, attribute_value)
VALUES (1, 'department', 'Support');
```

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
