<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseOrderResource\Pages;
use App\Models\Product;
use App\Models\PurchaseOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Adquisiciones & Proveedores';

    protected static ?string $modelLabel = 'Orden de Compra';

    protected static ?string $pluralModelLabel = 'Órdenes de Compra';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Cabecera de la Orden')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->label('Número de Orden')
                            ->default(fn () => 'OC-' . date('Y') . '-' . str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT))
                            ->required()
                            ->readOnly(),
                        Forms\Components\Select::make('supplier_id')
                            ->label('Proveedor')
                            ->relationship('supplier', 'business_name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('user_id')
                            ->label('Operador de Compras')
                            ->relationship('user', 'name')
                            ->default(fn () => auth()->id() ?? 1)
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Estado Transaccional')
                            ->options([
                                'BORRADOR' => 'Borrador',
                                'PENDIENTE_AUDITORIA' => 'Pendiente de Auditoría IA',
                                'APROBADO' => 'Aprobado para Pago',
                                'BLOQUEADO_POR_AUDITORIA' => 'Retenido por Auditoría (Bloqueado)',
                                'ANULADO' => 'Anulado',
                            ])
                            ->default('PENDIENTE_AUDITORIA')
                            ->required(),
                        Forms\Components\Select::make('audit_risk_level')
                            ->label('Nivel de Riesgo Evaluado')
                            ->options([
                                'BAJO' => 'Riesgo Bajo (Aprobado)',
                                'MEDIO' => 'Riesgo Medio (Advertencia)',
                                'CRITICO' => 'Riesgo Crítico (Bloqueado)',
                            ])
                            ->default('BAJO')
                            ->required(),
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Monto Total (Bs.)')
                            ->numeric()
                            ->prefix('Bs.')
                            ->readOnly()
                            ->default(0.00),
                    ])->columns(3),

                Forms\Components\Section::make('Detalle de Mercadería (Maestro-Detalle)')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('Ítem / SKU')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, callable $set) => 
                                        $set('unit_price', Product::find($state)?->weighted_avg_cost ?? 0.00)
                                    )
                                    ->required()
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('quantity')
                                    ->label('Cantidad')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->reactive()
                                    ->required()
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('unit_price')
                                    ->label('Precio Unitario (Bs.)')
                                    ->numeric()
                                    ->prefix('Bs.')
                                    ->minValue(0.01)
                                    ->reactive()
                                    ->required()
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('subtotal')
                                    ->label('Subtotal (Bs.)')
                                    ->numeric()
                                    ->prefix('Bs.')
                                    ->readOnly()
                                    ->columnSpan(1),
                            ])
                            ->columns(5)
                            ->defaultItems(1)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('N° Orden')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('supplier.business_name')
                    ->label('Proveedor')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Monto Total')
                    ->money('BOB')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'APROBADO' => 'success',
                        'BLOQUEADO_POR_AUDITORIA' => 'danger',
                        'PENDIENTE_AUDITORIA' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('audit_risk_level')
                    ->label('Riesgo IA')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'CRITICO' => 'danger',
                        'MEDIO' => 'warning',
                        'BAJO' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'APROBADO' => 'Aprobados',
                        'BLOQUEADO_POR_AUDITORIA' => 'Bloqueados por Auditoría',
                        'PENDIENTE_AUDITORIA' => 'Pendientes',
                    ])
                    ->label('Estado'),
                Tables\Filters\SelectFilter::make('audit_risk_level')
                    ->options([
                        'CRITICO' => 'Crítico',
                        'MEDIO' => 'Medio',
                        'BAJO' => 'Bajo',
                    ])
                    ->label('Nivel de Riesgo'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchaseOrders::route('/'),
            'create' => Pages\CreatePurchaseOrder::route('/create'),
            'edit' => Pages\EditPurchaseOrder::route('/{record}/edit'),
        ];
    }
}
