#!/usr/bin/env bash
set -euo pipefail

echo "=========================================="
echo " Levantar Servicios del ERP con Podman    "
echo " (PostgreSQL 16 pgvector + Redis)         "
echo "=========================================="

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(dirname "$SCRIPT_DIR")"

cd "$ROOT_DIR"

if ! command -v podman-compose >/dev/null 2>&1; then
    echo "❌ Error: podman-compose no está instalado o no se encuentra en el PATH."
    exit 1
fi

echo "🚀 Iniciando contenedores con podman-compose..."
podman-compose -f podman-compose.yaml up -d

echo ""
echo "⏳ Esperando a que PostgreSQL 16 y Redis estén listos..."
sleep 3

podman ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"

echo ""
echo "✅ Contenedores activos y listos para usar."
echo "   - PostgreSQL: 127.0.0.1:5432 (Base de datos: auditoria_ia)"
echo "   - Redis:      127.0.0.1:6379"
