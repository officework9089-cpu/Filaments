<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BrandsResource\Pages;
use App\Filament\Admin\Resources\BrandsResource\RelationManagers;
use App\Models\Brand;
use Doctrine\DBAL\Schema\Schema;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Livewire\Features\SupportConsoleCommands\Commands\MakeCommand;

class BrandsResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Shop';

    protected static ?int $navigationSort = 1;

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
        return [
            'Name' => $record->name,
            'Slug' => $record->slug,
            'Url' => $record->url,
            // 'Brands=> $record->brand->name'
        ];
    }
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
                    Section::make([
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
                    ->unique(),

                    TextInput::make('url')
                    ->label('Website URL')
                    ->required()
                    ->unique()
                    ->columnSpan('full'),

                    MarkdownEditor::make('description')
                    ->columnSpan('full')

                    ])->columns(2),
                ]),

                Group::make()
                ->schema([
                    Section::make('Status')
                    ->schema([
                        Toggle::make('is_visible')
                        ->label('Visibility')
                        ->helperText('Enable or disable brand visibility')
                        ->default(true)
                    ]),

                    Group::make()
                    ->schema([
                        Section::make('color')
                        ->schema([
                            ColorPicker::make('primary_hex')
                            ->label('Primary Color')
                        ])
                    ])
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                 TextColumn::make('name')
                ->searchable()
                ->sortable(),
                TextColumn::make('url')
                ->label('Website Url')
                ->searchable()
                ->sortable(),
                ColorColumn::make('primary_hex')
                ->label('Primary Color'),
                IconColumn::make('is_visible')
                ->sortable()
                ->boolean()
                ->label('Visibility'),
                TextColumn::make('updated_at')
                ->sortable()
                ->date(),
                
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrands::route('/create'),
            'edit' => Pages\EditBrands::route('/{record}/edit'),
        ];
    }
}
