#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(dirname "$SCRIPT_DIR")"

cd "$ROOT_DIR"

echo "🛑 Deteniendo contenedores de Podman..."
podman-compose -f podman-compose.yaml down

echo "✅ Contenedores detenidos correctamente."
