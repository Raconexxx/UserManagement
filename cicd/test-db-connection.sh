#!/usr/bin/env bash
set -euo pipefail

missing=0

if [[ -z "${DB_DB:-}" ]]; then
  echo "DB_DB fehlt."
  missing=1
fi

if [[ -z "${DB_USER:-}" ]]; then
  echo "DB_USER fehlt."
  missing=1
fi

if [[ -z "${DB_PASSWORD:-}" ]]; then
  echo "DB_PASSWORD fehlt."
  missing=1
fi

DB_HOST_VALUE="${DB_HOST:-${FTP_URL:-}}"

if [[ -z "$DB_HOST_VALUE" ]]; then
  echo "DB_HOST fehlt. Lege die Repository Variable DB_HOST an oder nutze FTP_URL als Fallback."
  missing=1
fi

if [[ "$missing" -ne 0 ]]; then
  exit 1
fi

echo "Teste Datenbankverbindung zu ${DB_HOST_VALUE}/${DB_DB} ..."

MYSQL_PWD="$DB_PASSWORD" mysql \
  --connect-timeout=20 \
  --host="$DB_HOST_VALUE" \
  --user="$DB_USER" \
  --database="$DB_DB" \
  --execute="SELECT 1;" \
  >/dev/null

echo "Datenbankverbindung erfolgreich."
