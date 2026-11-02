# ========================================================
# run-dev.ps1 - Inicializar Entorno ERP en Windows
# Andina S.R.L. - Auditoría IA
# ========================================================

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host " 🚀 Inicializando Entorno ERP (Windows)   " -ForegroundColor Cyan
Write-Host " Andina S.R.L. - Auditoría IA            " -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan

$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$RootDir = Split-Path -Parent $ScriptDir
Set-Location $RootDir

# 1. Levantar contenedores
& "$ScriptDir\podman-up.ps1"

# 2. Verificar Ollama
Write-Host "`n🔍 Verificando Ollama y nomic-embed-text..." -ForegroundColor Yellow
if (Get-Command ollama -ErrorAction SilentlyContinue) {
    $models = ollama list
    if ($models -match "nomic-embed-text") {
        Write-Host "✅ Modelo nomic-embed-text (768 dim) disponible en Ollama local." -ForegroundColor Green
    } else {
        Write-Host "⚠️ Descargando modelo nomic-embed-text..." -ForegroundColor Yellow
        ollama pull nomic-embed-text
        Write-Host "✅ Modelo descargado exitosamente." -ForegroundColor Green
    }
} else {
    Write-Host "⚠️ Ollama no encontrado en el PATH. Verifica que el servicio esté corriendo en http://localhost:11434" -ForegroundColor Yellow
}

Write-Host "`n==========================================" -ForegroundColor Cyan
Write-Host " ✅ Entorno de Servicios Listo           " -ForegroundColor Green
Write-Host " Comandos para iniciar la aplicación:     " -ForegroundColor White
Write-Host "   1. composer install                    " -ForegroundColor White
Write-Host "   2. Copy-Item .env.example .env (si no existe)" -ForegroundColor White
Write-Host "   3. php artisan key:generate            " -ForegroundColor White
Write-Host "   4. php artisan migrate:fresh --seed    " -ForegroundColor White
Write-Host "   5. php artisan serve                   " -ForegroundColor White
Write-Host "==========================================" -ForegroundColor Cyan
