<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-finger-print';

    protected static ?string $navigationGroup = 'Seguridad & Sistema';

    protected static ?string $modelLabel = 'Rol de Usuario';

    protected static ?string $pluralModelLabel = 'Roles y Permisos (Spatie)';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Definición del Rol')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Identificador del Rol')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100),
                        Forms\Components\TextInput::make('guard_name')
                            ->label('Guard')
                            ->default('web')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Permisos Asignados (RBAC)')
                    ->description('Marque los privilegios específicos otorgados a este rol')
                    ->schema([
                        Forms\Components\CheckboxList::make('permissions')
                            ->label('Lista de Permisos del Sistema')
                            ->relationship('permissions', 'name')
                            ->columns(3)
                            ->gridDirection('row')
                            ->bulkToggleable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre del Rol')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'gerente' => 'danger',
                        'auditor' => 'warning',
                        'compras' => 'info',
                        'almacen' => 'success',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('guard_name')
                    ->label('Guard')
                    ->sortable(),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->label('N° Permisos')
                    ->counts('permissions')
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha Creación')
                    ->dateTime('d/m/Y')
                    ->sortable(),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
