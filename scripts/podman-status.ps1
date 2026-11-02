# ========================================================
# podman-status.ps1 - Estado de Contenedores en Windows
# ========================================================

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host " Estado de Contenedores del ERP           " -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan

if (Get-Command podman -ErrorAction SilentlyContinue) {
    podman ps -a --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
} elseif (Get-Command docker -ErrorAction SilentlyContinue) {
    docker ps -a --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
} else {
    Write-Host "No se encontró podman o docker."
}
