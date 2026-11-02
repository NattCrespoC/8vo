<?php

declare(strict_types=1);

namespace App\Filament\Resources\AuditResolutionResource\Pages;

use App\Filament\Resources\AuditResolutionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAuditResolution extends CreateRecord
{
    protected static string $resource = AuditResolutionResource::class;
}
