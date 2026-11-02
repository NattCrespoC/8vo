<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\AuditResolutionResource\Pages;
use App\Models\AuditResolution;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AuditResolutionResource extends Resource
{
    protected static ?string $model = AuditResolution::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Auditoría & Inteligencia';

    protected static ?string $modelLabel = 'Dictamen de Auditoría';

    protected static ?string $pluralModelLabel = 'Centro de Auditoría IA';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Evaluación de Riesgo y Dictamen IA')
                    ->schema([
                        Forms\Components\Select::make('purchase_order_id')
                            ->label('Orden de Compra')
                            ->relationship('purchaseOrder', 'order_number')
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('risk_level')
                            ->label('Nivel de Riesgo')
                            ->options([
                                'BAJO' => 'Bajo',
                                'MEDIO' => 'Medio',
                                'CRITICO' => 'Crítico',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('confidence_percentage')
                            ->label('Nivel de Confianza IA (%)')
                            ->numeric()
                            ->suffix('%')
                            ->minValue(0)
                            ->maxValue(100),
                        Forms\Components\TextInput::make('financial_impact_bs')
                            ->label('Impacto Financiero Estimado (Bs.)')
                            ->numeric()
                            ->prefix('Bs.')
                            ->required(),
                        Forms\Components\Textarea::make('primary_reason')
                            ->label('Motivo Principal de Desvío')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('recommended_action')
                            ->label('Acción Recomendada por el Motor IA')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Resolución Administrativa')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Estado del Dictamen')
                            ->options([
                                'PENDIENTE_RESOLUCION' => 'Pendiente de Resolución Gerencial',
                                'APROBADO_CON_JUSTIFICACION' => 'Aprobado con Justificación Excepcional',
                                'ORDEN_ANULADA' => 'Confirmado Bloqueo y Orden Anulada',
                            ])
                            ->required(),
                        Forms\Components\Select::make('auditor_user_id')
                            ->label('Auditor / Evaluador')
                            ->relationship('auditorUser', 'name'),
                        Forms\Components\Textarea::make('resolution_notes')
                            ->label('Acta de Justificación / Descargo')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('purchaseOrder.order_number')
                    ->label('N° Orden')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('risk_level')
                    ->label('Riesgo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'CRITICO' => 'danger',
                        'MEDIO' => 'warning',
                        'BAJO' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('financial_impact_bs')
                    ->label('Impacto (Bs.)')
                    ->money('BOB')
                    ->sortable(),
                Tables\Columns\TextColumn::make('confidence_percentage')
                    ->label('Confianza')
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado Resolución')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ORDEN_ANULADA' => 'danger',
                        'APROBADO_CON_JUSTIFICACION' => 'info',
                        default => 'warning',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha Dictamen')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('risk_level')
                    ->options([
                        'CRITICO' => 'Crítico',
                        'MEDIO' => 'Medio',
                        'BAJO' => 'Bajo',
                    ]),
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
            'index' => Pages\ListAuditResolutions::route('/'),
            'create' => Pages\CreateAuditResolution::route('/create'),
            'edit' => Pages\EditAuditResolution::route('/{record}/edit'),
        ];
    }
}
