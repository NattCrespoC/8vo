# ========================================================
# podman-down.ps1 - Detener Servicios del ERP en Windows
# ========================================================

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host " Detener Servicios del ERP                " -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan

$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$RootDir = Split-Path -Parent $ScriptDir
Set-Location $RootDir

if (Get-Command podman-compose -ErrorAction SilentlyContinue) {
    podman-compose -f podman-compose.yaml down
} elseif (Get-Command podman -ErrorAction SilentlyContinue) {
    podman compose -f podman-compose.yaml down
} elseif (Get-Command docker-compose -ErrorAction SilentlyContinue) {
    docker-compose -f podman-compose.yaml down
} else {
    Write-Host "No se encontró podman o docker."
}

Write-Host "🛑 Servicios detenidos correctamente." -ForegroundColor Green
