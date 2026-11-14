<?php

namespace App\Filament\Resources\Recursos;

use App\Filament\Resources\Recursos\Pages\CreateRecurso;
use App\Filament\Resources\Recursos\Pages\EditRecurso;
use App\Filament\Resources\Recursos\Pages\ListRecursos;
use App\Filament\Resources\Recursos\Schemas\RecursoForm;
use App\Filament\Resources\Recursos\Tables\RecursosTable;
use App\Models\Recurso;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RecursoResource extends Resource
{
    protected static ?string $model = Recurso::class;

    protected static ?string $modelLabel = 'Recurso';
    protected static ?string $pluralModelLabel = 'Recursos';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return RecursoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RecursosTable::configure($table);
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
            'index' => ListRecursos::route('/'),
            'create' => CreateRecurso::route('/create'),
            'edit' => EditRecurso::route('/{record}/edit'),
        ];
    }
}
