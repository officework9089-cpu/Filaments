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
            ImportColumn::make('brand')
                ->requiredMapping()
                ->relationship('brand' , 'name')
                ->rules(['required']),    
            ImportColumn::make('slug')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('sku')
                ->label('SKU')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('description'),
            ImportColumn::make('image')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('quantity')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('price')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('is_visible'),
            ImportColumn::make('is_featured'),
            ImportColumn::make('type')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('publish_at')
                ->requiredMapping()
                ->rules(['required', 'date']),                

        ];
    }

    public function resolveRecord(): ?Products
    {
    
        return Products::firstOrNew($this->data, [
            // Update existing records, matching them by `$this->data['column_name']`
            'slug' => $this->data['slug'],
        ]);

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
