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

target_dir="${FTP_TARGET_DIR:-./}"
target_dir="${target_dir#./}"
target_dir="${target_dir#/}"

if [[ -z "$target_dir" ]]; then
  target_dir="./"
elif [[ "$target_dir" != */ ]]; then
  target_dir="${target_dir}/"
fi

echo "Lade Dateien nach ${FTP_URL}/${target_dir} hoch ..."

if [[ ! -d "web" ]]; then
  echo "Der Ordner web/ fehlt."
  exit 1
fi

lftp \
  -u "${FTP_USER},${FTP_PASSWORD}" \
  "ftp://${FTP_URL}" \
  -e "
set net:timeout 30
set net:max-retries 2
set net:reconnect-interval-base 5
set ftp:passive-mode on
set ftp:prefer-epsv no
set ftp:ssl-allow no
mirror --reverse --verbose --only-newer \
  web/ ${target_dir}
bye
"
