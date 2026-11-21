<?php

namespace App\Filament\Resources\LocalProvas;

use App\Filament\Resources\LocalProvas\Pages\CreateLocalProva;
use App\Filament\Resources\LocalProvas\Pages\EditLocalProva;
use App\Filament\Resources\LocalProvas\Pages\ListLocalProvas;
use App\Filament\Resources\LocalProvas\Schemas\LocalProvaForm;
use App\Filament\Resources\LocalProvas\Tables\LocalProvasTable;
use App\Models\LocalProva;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LocalProvaResource extends Resource
{
    protected static ?string $model = LocalProva::class;
    protected static ?string $modelLabel = 'Local de Prova';
    protected static ?string $pluralModelLabel = 'Locais de Prova';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string | \UnitEnum | null $navigationGroup = 'Locais & Alocação';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return LocalProvaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LocalProvasTable::configure($table);
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
            'index' => ListLocalProvas::route('/'),
            'create' => CreateLocalProva::route('/create'),
            'edit' => EditLocalProva::route('/{record}/edit'),
        ];
    }
}
