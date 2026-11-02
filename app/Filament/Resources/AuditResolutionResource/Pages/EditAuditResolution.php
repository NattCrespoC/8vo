<?php

declare(strict_types=1);

namespace App\Filament\Resources\AuditResolutionResource\Pages;

use App\Filament\Resources\AuditResolutionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAuditResolution extends EditRecord
{
    protected static string $resource = AuditResolutionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
