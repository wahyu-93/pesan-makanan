<?php

namespace App\Filament\Resources\TransactionItemResource\Pages;

use App\Filament\Resources\TransactionItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTransactionItems extends ManageRecords
{
    protected static string $resource = TransactionItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
