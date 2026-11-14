<?php

namespace App\Filament\Resources\Resultados;

use App\Filament\Resources\Resultados\Pages\CreateResultado;
use App\Filament\Resources\Resultados\Pages\EditResultado;
use App\Filament\Resources\Resultados\Pages\ListResultados;
use App\Filament\Resources\Resultados\Schemas\ResultadoForm;
use App\Filament\Resources\Resultados\Tables\ResultadosTable;
use App\Models\Resultado;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ResultadoResource extends Resource
{
    protected static ?string $model = Resultado::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ResultadoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ResultadosTable::configure($table);
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
            'index' => ListResultados::route('/'),
            'create' => CreateResultado::route('/create'),
            'edit' => EditResultado::route('/{record}/edit'),
        ];
    }
}
