<?php

namespace App\Filament\Resources\Provas;

use App\Filament\Resources\Provas\Pages\CreateProva;
use App\Filament\Resources\Provas\Pages\EditProva;
use App\Filament\Resources\Provas\Pages\ListProvas;
use App\Filament\Resources\Provas\Schemas\ProvaForm;
use App\Filament\Resources\Provas\Tables\ProvasTable;
use App\Models\Prova;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProvaResource extends Resource
{
    protected static ?string $model = Prova::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string | \UnitEnum | null $navigationGroup = 'Provas & Gabarito';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return ProvaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProvasTable::configure($table);
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
            'index' => ListProvas::route('/'),
            'create' => CreateProva::route('/create'),
            'edit' => EditProva::route('/{record}/edit'),
        ];
    }
}
