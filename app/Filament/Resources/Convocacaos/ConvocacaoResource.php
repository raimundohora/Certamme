<?php

namespace App\Filament\Resources\Convocacaos;

use App\Filament\Resources\Convocacaos\Pages\CreateConvocacao;
use App\Filament\Resources\Convocacaos\Pages\EditConvocacao;
use App\Filament\Resources\Convocacaos\Pages\ListConvocacaos;
use App\Filament\Resources\Convocacaos\Schemas\ConvocacaoForm;
use App\Filament\Resources\Convocacaos\Tables\ConvocacaosTable;
use App\Models\Convocacao;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use App\Filament\Concerns\ScopedToConcursoViaInscricao;

class ConvocacaoResource extends Resource
{
    use ScopedToConcursoViaInscricao;

    protected static ?string $model = Convocacao::class;

    protected static ?string $modelLabel = 'Convocação';
    protected static ?string $pluralModelLabel = 'Convocações';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string | \UnitEnum | null $navigationGroup = 'Locais & Alocação';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return ConvocacaoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ConvocacaosTable::configure($table);
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
            'index' => ListConvocacaos::route('/'),
            'create' => CreateConvocacao::route('/create'),
            'edit' => EditConvocacao::route('/{record}/edit'),
        ];
    }
}
