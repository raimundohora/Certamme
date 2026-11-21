<?php

namespace App\Filament\Resources\Cronogramas;

use App\Filament\Resources\Cronogramas\Pages\CreateCronograma;
use App\Filament\Resources\Cronogramas\Pages\EditCronograma;
use App\Filament\Resources\Cronogramas\Pages\ListCronogramas;
use App\Filament\Resources\Cronogramas\Schemas\CronogramaForm;
use App\Filament\Resources\Cronogramas\Tables\CronogramasTable;
use App\Models\Cronograma;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use App\Filament\Concerns\ScopedToConcurso;

class CronogramaResource extends Resource
{
    use ScopedToConcurso;

    protected static ?string $model = Cronograma::class;

    protected static ?string $modelLabel = 'Evento do cronograma';
    protected static ?string $pluralModelLabel = 'Cronograma';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string | \UnitEnum | null $navigationGroup = 'Configuração do Concurso';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return CronogramaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CronogramasTable::configure($table);
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
            'index' => ListCronogramas::route('/'),
            'create' => CreateCronograma::route('/create'),
            'edit' => EditCronograma::route('/{record}/edit'),
        ];
    }
}
