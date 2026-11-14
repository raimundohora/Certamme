<?php

namespace App\Filament\Resources\LeituraCartaos;

use App\Filament\Resources\LeituraCartaos\Pages\CreateLeituraCartao;
use App\Filament\Resources\LeituraCartaos\Pages\EditLeituraCartao;
use App\Filament\Resources\LeituraCartaos\Pages\ListLeituraCartaos;
use App\Filament\Resources\LeituraCartaos\Schemas\LeituraCartaoForm;
use App\Filament\Resources\LeituraCartaos\Tables\LeituraCartaosTable;
use App\Models\LeituraCartao;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LeituraCartaoResource extends Resource
{
    protected static ?string $model = LeituraCartao::class;

    protected static ?string $modelLabel = 'Leitura de cartão';
    protected static ?string $pluralModelLabel = 'Leituras / Gabarito-exclusivo';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return LeituraCartaoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeituraCartaosTable::configure($table);
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
            'index' => ListLeituraCartaos::route('/'),
            'create' => CreateLeituraCartao::route('/create'),
            'edit' => EditLeituraCartao::route('/{record}/edit'),
        ];
    }
}
