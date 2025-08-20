<?php

namespace App\Filament\Admin\Resources\BrandsResource\Pages;

use App\Filament\Admin\Resources\BrandsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBrands extends EditRecord
{
    protected static string $resource = BrandsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
