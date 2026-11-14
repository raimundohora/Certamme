<?php

namespace App\Filament\Resources\Concursos;

use App\Filament\Resources\Concursos\Pages\CreateConcurso;
use App\Filament\Resources\Concursos\Pages\EditConcurso;
use App\Filament\Resources\Concursos\Pages\ListConcursos;
use App\Filament\Resources\Concursos\Schemas\ConcursoForm;
use App\Filament\Resources\Concursos\Tables\ConcursosTable;
use App\Models\Concurso;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ConcursoResource extends Resource
{
    protected static ?string $model = Concurso::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ConcursoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ConcursosTable::configure($table);
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
            'index' => ListConcursos::route('/'),
            'create' => CreateConcurso::route('/create'),
            'edit' => EditConcurso::route('/{record}/edit'),
        ];
    }
}
