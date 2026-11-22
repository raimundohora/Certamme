<?php

namespace App\Filament\Resources\Heteroidentificacaos;

use App\Filament\Concerns\ScopedToConcursoViaInscricao;
use App\Filament\Resources\Heteroidentificacaos\Pages\CreateHeteroidentificacao;
use App\Filament\Resources\Heteroidentificacaos\Pages\EditHeteroidentificacao;
use App\Filament\Resources\Heteroidentificacaos\Pages\ListHeteroidentificacaos;
use App\Filament\Resources\Heteroidentificacaos\Schemas\HeteroidentificacaoForm;
use App\Filament\Resources\Heteroidentificacaos\Tables\HeteroidentificacaosTable;
use App\Models\Heteroidentificacao;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HeteroidentificacaoResource extends Resource
{
    use ScopedToConcursoViaInscricao;

    protected static ?string $model = Heteroidentificacao::class;

    protected static ?string $modelLabel = 'Heteroidentificação';
    protected static ?string $pluralModelLabel = 'Heteroidentificação';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static string | \UnitEnum | null $navigationGroup = 'Inscrições';

    protected static ?int $navigationSort = 35;

    public static function form(Schema $schema): Schema
    {
        return HeteroidentificacaoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HeteroidentificacaosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHeteroidentificacaos::route('/'),
            'create' => CreateHeteroidentificacao::route('/create'),
            'edit' => EditHeteroidentificacao::route('/{record}/edit'),
        ];
    }
}
