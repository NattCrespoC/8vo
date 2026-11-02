---
name: filament-development
description: "Best practices, UI component patterns, and real-time reactive implementations using FilamentPHP v3, Livewire 3, and Alpine.js. Use when creating or modifying Filament resources, pages, widgets, forms, tables, repeaters, action modals, badges, or listening to WebSocket broadcasting events."
---

# FilamentPHP v3 Development Skill

This skill guides the construction of responsive, modern administrative panels using FilamentPHP v3.2 in Laravel 11.

## 1. Resource Structure & Organization
- Always separate Resources into discrete directories under `app/Filament/Resources/`.
- Maintain clean navigation groups:
  - `Adquisiciones & Proveedores` (Proveedores, Órdenes de Compra)
  - `Almacenes & Kardex` (Productos, Movimientos, Recepción Multimodal)
  - `Auditoría & Inteligencia` (Centro de Auditoría IA, Bitácora de Riesgos, Copiloto Text-to-SQL)
  - `Seguridad & Sistema` (Usuarios, Roles, Auditorías de Acceso)

## 2. Form Conventions (`form(Form $form)`)
- Structure forms with semantic sections: `Section::make('Título')->columns(2)`.
- Use master-detail repeaters for purchase orders:
  ```php
  Repeater::make('items')
      ->relationship()
      ->schema([
          Select::make('product_id')
              ->relationship('product', 'name')
              ->searchable()
              ->preload()
              ->reactive()
              ->afterStateUpdated(fn ($state, callable $set) => 
                  $set('unit_price', Product::find($state)?->weighted_avg_cost ?? 0)
              )
              ->required(),
          TextInput::make('quantity')
              ->numeric()
              ->default(1)
              ->minValue(1)
              ->reactive()
              ->required(),
          TextInput::make('unit_price')
              ->numeric()
              ->prefix('Bs.')
              ->reactive()
              ->required(),
      ])
      ->columns(3)
  ```

## 3. Table Conventions (`table(Table $table)`)
- Use semantic status badges:
  ```php
  TextColumn::make('risk_level')
      ->label('Nivel de Riesgo')
      ->badge()
      ->color(fn (string $state): string => match ($state) {
          'BAJO' => 'success',
          'MEDIO' => 'warning',
          'ALTO' => 'danger',
          'CRITICO' => 'danger',
          default => 'gray',
      })
      ->sortable(),
  ```
- Add custom Action Modals to inspect AI audit reports:
  ```php
  Action::make('ver_dictamen')
      ->label('Dictamen IA')
      ->icon('heroicon-o-sparkles')
      ->color('info')
      ->visible(fn (PurchaseOrder $record) => $record->ai_audit_report !== null)
      ->modalHeading(fn (PurchaseOrder $record) => "Dictamen Técnico - Orden #{$record->order_number}")
      ->modalContent(fn (PurchaseOrder $record) => view('filament.modals.ai-audit-report', [
          'report' => $record->ai_audit_report,
          'order' => $record,
      ]))
  ```

## 4. Real-time Reactive Listeners (WebSockets / Echo)
- In List pages (e.g. `ListPurchaseOrders`) or Custom Pages, listen to live Reverb broadcasts:
  ```php
  use Livewire\Attributes\On;
  use Filament\Notifications\Notification;

  #[On('echo:audit-channel,AnomalyDetectedEvent')]
  public function onAnomalyDetected(array $payload): void
  {
      Notification::make()
          ->title('¡ALERTA DE AUDITORÍA CRÍTICA!')
          ->body("Orden #{$payload['order_number']}: {$payload['reason']} (Desvío Bs. {$payload['impact_bs']})")
          ->danger()
          ->persistent()
          ->actions([
              \Filament\Notifications\Actions\Action::make('revisar')
                  ->button()
                  ->url(route('filament.admin.resources.purchase-orders.edit', $payload['order_id'])),
          ])
          ->send();

      $this->resetTable();
  }
  ```

## 5. Filament Dashboard Widgets
- Display executive KPIs:
  - `StatsOverviewWidget`: Total Ahorrado en Bs., Compras Auditadas, Transacciones Bloqueadas, Tiempo Medio de Detección (< 1.5s).
  - `ChartWidget`: Línea de tiempo de desvíos y anomalías detectadas por semana.
