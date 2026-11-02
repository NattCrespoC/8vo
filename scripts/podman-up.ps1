# ========================================================
# podman-up.ps1 - Levantar Servicios del ERP en Windows
# (PostgreSQL 16 con pgvector + Redis)
# ========================================================

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host " Levantar Servicios del ERP con Podman    " -ForegroundColor Cyan
Write-Host " (PostgreSQL 16 pgvector + Redis)         " -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan

$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$RootDir = Split-Path -Parent $ScriptDir
Set-Location $RootDir

# Verificar disponibilidad de podman-compose o podman compose
if (Get-Command podman-compose -ErrorAction SilentlyContinue) {
    Write-Host "🚀 Iniciando contenedores con podman-compose..." -ForegroundColor Green
    podman-compose -f podman-compose.yaml up -d
} elseif (Get-Command podman -ErrorAction SilentlyContinue) {
    Write-Host "🚀 Iniciando contenedores con podman compose..." -ForegroundColor Green
    podman compose -f podman-compose.yaml up -d
} elseif (Get-Command docker-compose -ErrorAction SilentlyContinue) {
    Write-Host "⚠️ podman no encontrado, usando docker-compose..." -ForegroundColor Yellow
    docker-compose -f podman-compose.yaml up -d
} else {
    Write-Error "❌ Error: Ni podman ni docker están instalados o en el PATH."
    Exit 1
}

Write-Host "`n⏳ Esperando a que PostgreSQL 16 y Redis estén listos..." -ForegroundColor Yellow
Start-Sleep -Seconds 3

if (Get-Command podman -ErrorAction SilentlyContinue) {
    podman ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
} else {
    docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
}

Write-Host "`n✅ Contenedores activos y listos para usar." -ForegroundColor Green
Write-Host "   - PostgreSQL: 127.0.0.1:5432 (Base de datos: auditoria_ia, Usuario: postgres, Pass: postgres)" -ForegroundColor White
Write-Host "   - Redis:      127.0.0.1:6379" -ForegroundColor White
