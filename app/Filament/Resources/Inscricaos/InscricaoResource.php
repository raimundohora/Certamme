<?php

namespace App\Filament\Resources\Inscricaos;

use App\Filament\Resources\Inscricaos\Pages\CreateInscricao;
use App\Filament\Resources\Inscricaos\Pages\EditInscricao;
use App\Filament\Resources\Inscricaos\Pages\ListInscricaos;
use App\Filament\Resources\Inscricaos\Schemas\InscricaoForm;
use App\Filament\Resources\Inscricaos\Tables\InscricaosTable;
use App\Models\Inscricao;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use App\Filament\Concerns\ScopedToConcurso;

class InscricaoResource extends Resource
{
    use ScopedToConcurso;

    protected static ?string $model = Inscricao::class;
    protected static ?string $modelLabel = 'Inscrição';
    protected static ?string $pluralModelLabel = 'Inscrições';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string | \UnitEnum | null $navigationGroup = 'Inscrições';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return InscricaoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InscricaosTable::configure($table);
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
            'index' => ListInscricaos::route('/'),
            'create' => CreateInscricao::route('/create'),
            'edit' => EditInscricao::route('/{record}/edit'),
        ];
    }
}
