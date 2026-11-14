<?php

namespace App\Filament\Resources\GabaritoItems;

use App\Filament\Resources\GabaritoItems\Pages\CreateGabaritoItem;
use App\Filament\Resources\GabaritoItems\Pages\EditGabaritoItem;
use App\Filament\Resources\GabaritoItems\Pages\ListGabaritoItems;
use App\Filament\Resources\GabaritoItems\Schemas\GabaritoItemForm;
use App\Filament\Resources\GabaritoItems\Tables\GabaritoItemsTable;
use App\Models\GabaritoItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GabaritoItemResource extends Resource
{
    protected static ?string $model = GabaritoItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return GabaritoItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GabaritoItemsTable::configure($table);
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
            'index' => ListGabaritoItems::route('/'),
            'create' => CreateGabaritoItem::route('/create'),
            'edit' => EditGabaritoItem::route('/{record}/edit'),
        ];
    }
}
