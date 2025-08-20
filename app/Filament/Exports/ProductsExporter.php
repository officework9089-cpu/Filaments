<?php

namespace App\Filament\Exports;

use App\Models\Products;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProductsExporter extends Exporter
{
    protected static ?string $model = Products::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('name'),
            ExportColumn::make('slug'),
            ExportColumn::make('sku')
                ->label('SKU'),
            ExportColumn::make('description'),
            ExportColumn::make('quantity'),
            ExportColumn::make('price'),
            ExportColumn::make('is_visible'),
            ExportColumn::make('is_faetured'),
            ExportColumn::make('type'),

        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your products export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
