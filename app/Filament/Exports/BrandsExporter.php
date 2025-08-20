<?php

namespace App\Filament\Exports;

use App\Models\brand;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class BrandsExporter extends Exporter
{
    protected static ?string $model = brand::class;

    public static function getColumns(): array
    {
        return [
            //
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your brands export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
