<?php

namespace App\Filament\Imports;

use App\Models\Products;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ProductsImporter extends Importer
{
    protected static ?string $model = Products::class;

    public static function getColumns(): array
    {
        return [

            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('slug')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('sku')
                ->label('SKU')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('description'),
            ImportColumn::make('quantity')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('price')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('is_visible')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('is_faetured')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('type')
                ->requiredMapping()
                ->rules(['required']),

        ];
    }

    public function resolveRecord(): ?Products
    {
        // return Products::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Products();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your products import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
