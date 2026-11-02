#!/usr/bin/env bash
set -euo pipefail

echo "=========================================="
echo " Estado de Contenedores Podman            "
echo "=========================================="

podman ps -a --filter "name=erp_auditoria_" --format "table {{.ID}}\t{{.Names}}\t{{.Status}}\t{{.Ports}}"
