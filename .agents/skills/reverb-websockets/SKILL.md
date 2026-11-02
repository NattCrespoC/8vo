---
name: reverb-websockets
description: "Laravel Reverb server setup, WebSocket event broadcasting, real-time alert handling, and Livewire/Echo channel integration. Use when setting up broadcasting configuration, creating ShouldBroadcast/ShouldBroadcastNow events, configuring Reverb authentication, or receiving real-time push notifications on the frontend."
---

# Laravel Reverb WebSockets Skill

This skill outlines the configuration, event dispatching, and frontend reception of real-time alerts using Laravel Reverb in Laravel 11.

## 1. Reverb Architecture in the Project
- **Puerto:** `8080` (predeterminado de Laravel Reverb).
- **Protocolo:** WebSocket bidireccional de baja latencia ($< 50\text{ ms}$).
- **Canal Principal de Auditoría:** `audit-channel` (Canal de presencia o privado autenticado).

---

## 2. Evento de Alerta Inmediata (`AnomalyDetectedEvent.php`)

```php
<?php declare(strict_types=1);

namespace App\Events;

use App\Models\PurchaseOrder;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AnomalyDetectedEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly PurchaseOrder $order,
        public readonly string $riskLevel,
        public readonly string $primaryReason,
        public readonly float $financialImpactBs,
        public readonly array $anomalousItems
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('audit-channel'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'AnomalyDetectedEvent';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'supplier_name' => $this->order->supplier->business_name,
            'total_amount' => (float) $this->order->total_amount,
            'risk_level' => $this->riskLevel,
            'reason' => $this->primaryReason,
            'impact_bs' => $this->financialImpactBs,
            'items' => $this->anomalousItems,
            'detected_at' => now()->toIso8601String(),
        ];
    }
}
```

---

## 3. Configuración en `.env`

```env
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=sis_auditoria_app
REVERB_APP_KEY=sis_auditoria_key
REVERB_APP_SECRET=sis_auditoria_secret
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

---

## 4. Recepción en el Dashboard de Filament / Livewire

En `app/Filament/Widgets/AuditLiveAlertsWidget.php` o páginas administrativas:

```php
use Livewire\Attributes\On;

#[On('echo:audit-channel,AnomalyDetectedEvent')]
public function handleIncomingAnomaly(array $data): void
{
    // 1. Añadir al feed en memoria reactiva
    array_unshift($this->recentAlerts, $data);
    $this->recentAlerts = array_slice($this->recentAlerts, 0, 10);

    // 2. Disparar notificación visual nativa de Filament
    \Filament\Notifications\Notification::make()
        ->title("¡Transacción Retenida: {$data['order_number']}!")
        ->body("Riesgo: {$data['risk_level']}. {$data['reason']} (Impacto: Bs. {$data['impact_bs']})")
        ->danger()
        ->persistent()
        ->send();

    // 3. Emitir sonido de alerta mediante JavaScript en el navegador
    $this->dispatch('play-alert-sound', severity: $data['risk_level']);
}
```
