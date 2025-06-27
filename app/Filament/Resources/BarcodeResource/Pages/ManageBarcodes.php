<?php

namespace App\Filament\Resources\BarcodeResource\Pages;

use App\Filament\Resources\BarcodeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBarcodes extends ManageRecords
{
    protected static string $resource = BarcodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
