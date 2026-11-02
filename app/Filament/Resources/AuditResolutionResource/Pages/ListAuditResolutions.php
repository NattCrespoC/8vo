<?php

declare(strict_types=1);

namespace App\Filament\Resources\AuditResolutionResource\Pages;

use App\Filament\Resources\AuditResolutionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAuditResolutions extends ListRecords
{
    protected static string $resource = AuditResolutionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Nuevo Dictamen'),
        ];
    }
}
