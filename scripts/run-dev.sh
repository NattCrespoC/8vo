#!/usr/bin/env bash
set -euo pipefail

echo "=========================================="
echo " 🚀 Inicializando Entorno ERP (Linux)    "
echo " Andina S.R.L. - Auditoría IA            "
echo "=========================================="

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(dirname "$SCRIPT_DIR")"
cd "$ROOT_DIR"

# 1. Levantar contenedores
"$SCRIPT_DIR/podman-up.sh"

# 2. Verificar Ollama y modelo de embeddings
echo ""
echo "🔍 Verificando Ollama y nomic-embed-text..."
if command -v ollama >/dev/null 2>&1; then
    if ollama list | grep -q "nomic-embed-text"; then
        echo "✅ Modelo nomic-embed-text (768 dim) disponible en Ollama local."
    else
        echo "⚠️ Modelo nomic-embed-text no encontrado. Descargando..."
        ollama pull nomic-embed-text
        echo "✅ Modelo descargado exitosamente."
    fi
else
    echo "⚠️ Ollama no encontrado en el PATH. Asegúrate de tener Ollama corriendo en http://localhost:11434"
fi

echo ""
echo "=========================================="
echo " ✅ Entorno de Servicios Listo           "
echo " Comandos para iniciar la aplicación:     "
echo "   1. composer install                    "
echo "   2. cp .env.example .env (si aplica)    "
echo "   3. php artisan key:generate            "
echo "   4. php artisan migrate:fresh --seed    "
echo "   5. php artisan serve                   "
echo "=========================================="
