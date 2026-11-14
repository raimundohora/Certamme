<?php

namespace App\Filament\Resources\Alocacaos;

use App\Filament\Resources\Alocacaos\Pages\CreateAlocacao;
use App\Filament\Resources\Alocacaos\Pages\EditAlocacao;
use App\Filament\Resources\Alocacaos\Pages\ListAlocacaos;
use App\Filament\Resources\Alocacaos\Schemas\AlocacaoForm;
use App\Filament\Resources\Alocacaos\Tables\AlocacaosTable;
use App\Models\Alocacao;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AlocacaoResource extends Resource
{
    protected static ?string $model = Alocacao::class;
    protected static ?string $modelLabel = 'Alocação';
    protected static ?string $pluralModelLabel = 'Mapa de Sala';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return AlocacaoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AlocacaosTable::configure($table);
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
            'index' => ListAlocacaos::route('/'),
            'create' => CreateAlocacao::route('/create'),
            'edit' => EditAlocacao::route('/{record}/edit'),
        ];
    }
}
