<?php

namespace App\Filament\Admin\Resources\BrandsResource\Pages;

use App\Filament\Admin\Resources\BrandsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBrands extends ListRecords
{
    protected static string $resource = BrandsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
