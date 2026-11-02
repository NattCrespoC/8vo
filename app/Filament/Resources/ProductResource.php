<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Almacenes & Kardex';

    protected static ?string $modelLabel = 'Producto';

    protected static ?string $pluralModelLabel = 'Catálogo de Productos';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identificación del Producto')
                    ->schema([
                        Forms\Components\TextInput::make('sku')
                            ->label('Código SKU')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del Producto')
                            ->required()
                            ->maxLength(200),
                        Forms\Components\Select::make('category_id')
                            ->label('Categoría')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('unit_of_measure')
                            ->label('Unidad de Medida')
                            ->options([
                                'PZA' => 'Pieza / Unidad (PZA)',
                                'CJ' => 'Caja (CJ)',
                                'BAR' => 'Barra (BAR)',
                                'GLN' => 'Galón (GLN)',
                                'KG' => 'Kilogramo (KG)',
                                'MTR' => 'Metro (MTR)',
                            ])
                            ->default('PZA')
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción Técnica')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Control de Existencias (Stock)')
                    ->schema([
                        Forms\Components\TextInput::make('current_stock')
                            ->label('Stock Actual')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->required(),
                        Forms\Components\TextInput::make('min_stock')
                            ->label('Stock Mínimo de Alerta')
                            ->numeric()
                            ->default(10)
                            ->minValue(1)
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Ítem Activo para Transacciones')
                            ->default(true),
                    ])->columns(3),

                Forms\Components\Section::make('Valoración Contable y Costos (Bolivianos)')
                    ->description('Cálculo de Costo Promedio Ponderado (CPP) y parámetros para el motor Z-Score')
                    ->schema([
                        Forms\Components\TextInput::make('weighted_avg_cost')
                            ->label('Costo Promedio Ponderado (CPP)')
                            ->numeric()
                            ->prefix('Bs.')
                            ->default(0.00)
                            ->minValue(0)
                            ->required(),
                        Forms\Components\TextInput::make('std_dev_cost')
                            ->label('Desviación Estándar de Costo (σ)')
                            ->numeric()
                            ->prefix('Bs.')
                            ->default(0.00)
                            ->minValue(0),
                        Forms\Components\TextInput::make('last_purchase_price')
                            ->label('Último Precio de Compra')
                            ->numeric()
                            ->prefix('Bs.')
                            ->default(0.00)
                            ->minValue(0),
                        Forms\Components\TextInput::make('standard_sale_price')
                            ->label('Precio Estándar de Venta')
                            ->numeric()
                            ->prefix('Bs.')
                            ->default(0.00)
                            ->minValue(0),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Producto')
                    ->searchable()
                    ->sortable()
                    ->limit(35),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoría')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_stock')
                    ->label('Stock')
                    ->sortable()
                    ->badge()
                    ->color(fn (Product $record): string => match (true) {
                        $record->current_stock <= $record->min_stock => 'danger',
                        $record->current_stock <= ($record->min_stock * 1.5) => 'warning',
                        default => 'success',
                    }),
                Tables\Columns\TextColumn::make('weighted_avg_cost')
                    ->label('CPP (Bs.)')
                    ->money('BOB')
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_purchase_price')
                    ->label('Última Compra')
                    ->money('BOB')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->label('Filtrar por Categoría'),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
