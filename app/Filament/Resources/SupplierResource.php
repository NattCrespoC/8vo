<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Adquisiciones & Proveedores';

    protected static ?string $modelLabel = 'Proveedor';

    protected static ?string $pluralModelLabel = 'Catálogo de Proveedores';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Fiscal y Comercial')
                    ->description('Datos validados ante el Servicio de Impuestos Nacionales')
                    ->schema([
                        Forms\Components\TextInput::make('nit')
                            ->label('NIT')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(30),
                        Forms\Components\TextInput::make('business_name')
                            ->label('Razón Social')
                            ->required()
                            ->maxLength(150),
                        Forms\Components\TextInput::make('trade_name')
                            ->label('Nombre Comercial')
                            ->maxLength(150),
                        Forms\Components\Select::make('category_id')
                            ->label('Rubro Comercial')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Contacto y Localización')
                    ->schema([
                        Forms\Components\TextInput::make('contact_name')
                            ->label('Persona de Contacto')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('phone')
                            ->label('Teléfono / Celular')
                            ->tel()
                            ->maxLength(50),
                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('city')
                            ->label('Ciudad')
                            ->default('Cochabamba')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('address')
                            ->label('Dirección Comercial')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Auditoría y Evaluación de Riesgo')
                    ->schema([
                        Forms\Components\TextInput::make('risk_score')
                            ->label('Calificación de Riesgo (0-100)')
                            ->numeric()
                            ->default(0.00)
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('/ 100'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Proveedor Habilitado')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nit')
                    ->label('NIT')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('business_name')
                    ->label('Razón Social')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Rubro')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Ciudad')
                    ->sortable(),
                Tables\Columns\TextColumn::make('risk_score')
                    ->label('Riesgo')
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        (float) $state >= 60.0 => 'danger',
                        (float) $state >= 25.0 => 'warning',
                        default => 'success',
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->label('Filtrar por Rubro'),
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
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
