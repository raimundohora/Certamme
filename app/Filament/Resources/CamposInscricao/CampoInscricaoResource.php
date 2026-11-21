<?php

namespace App\Filament\Resources\CamposInscricao;

use App\Filament\Resources\CamposInscricao\Pages\CreateCampoInscricao;
use App\Filament\Resources\CamposInscricao\Pages\EditCampoInscricao;
use App\Filament\Resources\CamposInscricao\Pages\ListCamposInscricao;
use App\Filament\Resources\CamposInscricao\Schemas\CampoInscricaoForm;
use App\Filament\Resources\CamposInscricao\Tables\CamposInscricaoTable;
use App\Models\CampoInscricao;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use App\Filament\Concerns\ScopedToConcurso;

class CampoInscricaoResource extends Resource
{
    use ScopedToConcurso;

    protected static ?string $model = CampoInscricao::class;

    protected static ?string $slug = 'campos-inscricao';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedListBullet;

    protected static string | \UnitEnum | null $navigationGroup = 'Configuração do Concurso';

    protected static ?int $navigationSort = 25;

    protected static ?string $navigationLabel = 'Campos de inscrição';

    protected static ?string $modelLabel = 'Campo de inscrição';

    protected static ?string $pluralModelLabel = 'Campos de inscrição';

    public static function form(Schema $schema): Schema
    {
        return CampoInscricaoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CamposInscricaoTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCamposInscricao::route('/'),
            'create' => CreateCampoInscricao::route('/create'),
            'edit' => EditCampoInscricao::route('/{record}/edit'),
        ];
    }
}
