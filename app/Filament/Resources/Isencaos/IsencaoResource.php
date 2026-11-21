<?php

namespace App\Filament\Resources\Isencaos;

use App\Filament\Resources\Isencaos\Pages\CreateIsencao;
use App\Filament\Resources\Isencaos\Pages\EditIsencao;
use App\Filament\Resources\Isencaos\Pages\ListIsencaos;
use App\Filament\Resources\Isencaos\Schemas\IsencaoForm;
use App\Filament\Resources\Isencaos\Tables\IsencaosTable;
use App\Models\Isencao;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use App\Filament\Concerns\ScopedToConcursoViaInscricao;

class IsencaoResource extends Resource
{
    use ScopedToConcursoViaInscricao;

    protected static ?string $model = Isencao::class;

    protected static ?string $modelLabel = 'Isenção';
    protected static ?string $pluralModelLabel = 'Isenções';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string | \UnitEnum | null $navigationGroup = 'Inscrições';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return IsencaoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IsencaosTable::configure($table);
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
            'index' => ListIsencaos::route('/'),
            'create' => CreateIsencao::route('/create'),
            'edit' => EditIsencao::route('/{record}/edit'),
        ];
    }
}
