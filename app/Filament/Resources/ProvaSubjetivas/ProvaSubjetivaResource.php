<?php

namespace App\Filament\Resources\ProvaSubjetivas;

use App\Filament\Resources\ProvaSubjetivas\Pages\CreateProvaSubjetiva;
use App\Filament\Resources\ProvaSubjetivas\Pages\EditProvaSubjetiva;
use App\Filament\Resources\ProvaSubjetivas\Pages\ListProvaSubjetivas;
use App\Filament\Resources\ProvaSubjetivas\Schemas\ProvaSubjetivaForm;
use App\Filament\Resources\ProvaSubjetivas\Tables\ProvaSubjetivasTable;
use App\Models\ProvaSubjetiva;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProvaSubjetivaResource extends Resource
{
    protected static ?string $model = ProvaSubjetiva::class;
    protected static ?string $modelLabel = 'Prova Subjetiva';
    protected static ?string $pluralModelLabel = 'Provas Subjetivas';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string | \UnitEnum | null $navigationGroup = 'Provas & Gabarito';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return ProvaSubjetivaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProvaSubjetivasTable::configure($table);
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
            'index' => ListProvaSubjetivas::route('/'),
            'create' => CreateProvaSubjetiva::route('/create'),
            'edit' => EditProvaSubjetiva::route('/{record}/edit'),
        ];
    }
}
