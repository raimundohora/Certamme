<?php

namespace App\Filament\Resources\TituloTipos;

use App\Filament\Resources\TituloTipos\Pages\CreateTituloTipo;
use App\Filament\Resources\TituloTipos\Pages\EditTituloTipo;
use App\Filament\Resources\TituloTipos\Pages\ListTituloTipos;
use App\Filament\Resources\TituloTipos\Schemas\TituloTipoForm;
use App\Filament\Resources\TituloTipos\Tables\TituloTiposTable;
use App\Models\TituloTipo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TituloTipoResource extends Resource
{
    protected static ?string $model = TituloTipo::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return TituloTipoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TituloTiposTable::configure($table);
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
            'index' => ListTituloTipos::route('/'),
            'create' => CreateTituloTipo::route('/create'),
            'edit' => EditTituloTipo::route('/{record}/edit'),
        ];
    }
}
