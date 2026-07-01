#!/usr/bin/env bash
set -euo pipefail

missing=0

if [[ -z "${FTP_URL:-}" ]]; then
  echo "FTP_URL fehlt."
  missing=1
fi

if [[ -z "${FTP_USER:-}" ]]; then
  echo "FTP_USER fehlt."
  missing=1
fi

if [[ -z "${FTP_PASSWORD:-}" ]]; then
  echo "FTP_PASSWORD fehlt."
  missing=1
fi

if [[ "$missing" -ne 0 ]]; then
  exit 1
fi

echo "Teste FTP-Verbindung zu ${FTP_URL} ..."

curl \
  --fail \
  --silent \
  --show-error \
  --connect-timeout 20 \
  --max-time 60 \
  --user "${FTP_USER}:${FTP_PASSWORD}" \
  "ftp://${FTP_URL}/" \
  >/dev/null

echo "FTP-Verbindung erfolgreich."
