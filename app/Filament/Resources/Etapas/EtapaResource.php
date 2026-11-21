<?php

namespace App\Filament\Resources\Etapas;

use App\Filament\Resources\Etapas\Pages\CreateEtapa;
use App\Filament\Resources\Etapas\Pages\EditEtapa;
use App\Filament\Resources\Etapas\Pages\ListEtapas;
use App\Filament\Resources\Etapas\Schemas\EtapaForm;
use App\Filament\Resources\Etapas\Tables\EtapasTable;
use App\Models\Etapa;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use App\Filament\Concerns\ScopedToConcurso;

class EtapaResource extends Resource
{
    use ScopedToConcurso;

    protected static ?string $model = Etapa::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string | \UnitEnum | null $navigationGroup = 'Configuração do Concurso';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return EtapaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EtapasTable::configure($table);
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
            'index' => ListEtapas::route('/'),
            'create' => CreateEtapa::route('/create'),
            'edit' => EditEtapa::route('/{record}/edit'),
        ];
    }
}
