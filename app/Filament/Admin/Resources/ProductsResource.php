<?php

namespace App\Filament\Admin\Resources;

use App\Enums\ProductTypeEnum as EnumsProductTypeEnum;
use App\Filament\Admin\Resources\ProductsResource\Pages;
use App\Filament\Admin\Resources\ProductsResource\RelationManagers;
use App\Filament\Exports\ProductsExporter;
use App\Filament\Imports\ProductsImporter;
use App\Models\Products;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Schema;
use Filament\Actions\Exports\Enums\Contracts\ExportFormat;
use Filament\Actions\Imports\Importer;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;


use function Laravel\Prompts\select;

class ProductsResource extends Resource
{
    protected static ?string $model = Products::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';
    
    protected static ?string $navigationGroup = 'Shop';

    protected static ?string $navigationLabel = 'Products';

    protected static ?string $activeNavigationIcon = 'heroicon-o-check-badge';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    protected static ?int $navigationSort =0 ;

     public static function getRecordTitleAttribute(): ?string
{
    return "{name} ({url})";
}

    protected static int $globalSearchResultsLimit = 20;


    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'slug', 'url', 'description'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return[
            'Brand' => $record->brand->name,
        ];
    }
    // public static function getGlobalSearchResultDetails(Model $record): array
    // {
    //     return [
    //         'Brands=> $record->brand->name'
    //     ];
    // }
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['brand']);
    }




    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                ->schema([
                    Section::make()
                    ->schema([
                    TextInput::make('name')
                    ->required()
                    ->live( onBlur: true)
                    ->unique()
                    ->afterStateUpdated(function(string $operation, $state, Forms\Set $set){
                        if ($operation !== 'create'){
                            return;
                        }
                        $set('slug', Str::slug($state));
                    }),
                    TextInput::make('slug')
                    ->disabled()
                    ->dehydrated()
                    ->required()
                    ->unique(Products::class, 'slug', ignoreRecord:true),
                    MarkdownEditor::make('description')
                    ->columnSpan('full')
                ])->columns(2),
                    Section::make('Pricing And Inventory')
                    ->schema([
                    TextInput::make('sku')
                    ->label('SKU (Stock Keeping Unit)')
                    ->unique()
                    ->required(),
                    TextInput::make('price')
                    ->numeric()
                    ->rules('regex:/^\d{1,6}(\.\d{0,2})?$/')
                    ->required(),
                    TextInput::make('quantity')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->required(),
                    Select::make('type')
                    ->options([
                        'downloadable' => EnumsProductTypeEnum::DOWNLOADABLE->value,
                        'deliverable' => EnumsProductTypeEnum::DELIVERABLE->value,
                    ])->required(),
                ])->columns(2),

                    ]),
                Group::make()
                ->schema([
                    Section::make('Status')
                    ->schema([
                    Toggle::make('is_visible')
                    ->label('Visibility')
                    ->helperText("Enable or disable Product Visibility")
                    ->default(true),
                    Toggle::make('is_featured')
                    ->label('Featured')
                    ->helperText("Enable or disable Product Featured"),
                    DatePicker::make('publish_at')
                    ->label('Availibility')
                    ->default(now())
                    ]),
                    Section::make('Image')
                    ->schema([
                    FileUpload::make('image')
                    ->directory('form-attachment')
                    ->preserveFilenames()
                    ->image()
                    ->imageEditor(),
                ])->collapsible(),
                    Section::make('Associations')
                    ->schema([
                    Select::make('brand_id')
                    ->relationship('brand', 'name')
                    ->required(),
                    
                ]),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image'),
                TextColumn::make('name')
                ->searchable()
                ->sortable(),
                TextColumn::make('brand.name')
                ->searchable()
                ->sortable()
                ->toggleable(),
                TextColumn::make('category.name')
                ->searchable()
                ->sortable()
                ->toggleable(),
                IconColumn::make('is_visible')
                ->sortable()
                ->toggleable()
                ->label('Visibility')
                ->boolean(),
                TextColumn::make('price')
                ->sortable()
                ->toggleable(),
                TextColumn::make('quantity')
                ->sortable()
                ->toggleable(),
                TextColumn::make('publish_at')
                ->sortable()
                ->date(),
                TextColumn::make('type'),

            ])
            ->filters([
                TernaryFilter::make('is_visible')
                ->label('Visibility')
                ->boolean()
                ->trueLabel('Only Visible Products')
                ->falseLabel('Only Hidden Products')
                ->native(false),

                SelectFilter::make('brand')
                ->relationship('brand', 'name')
            ])
            ->actions([
              Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
             ])
             ->headerActions([
                ExportAction::make()->exporter(ProductsExporter::class),
                ImportAction::make()->Importer(ProductsImporter::class)
             ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                ExportBulkAction::make()->exporter(ProductsExporter::class)
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProducts::route('/create'),
            'edit' => Pages\EditProducts::route('/{record}/edit'),
        ];
    }
}
