<?php

namespace App\Filament\Resources\CorrecaoSubjetivas;

use App\Filament\Resources\CorrecaoSubjetivas\Pages\CreateCorrecaoSubjetiva;
use App\Filament\Resources\CorrecaoSubjetivas\Pages\EditCorrecaoSubjetiva;
use App\Filament\Resources\CorrecaoSubjetivas\Pages\ListCorrecaoSubjetivas;
use App\Filament\Resources\CorrecaoSubjetivas\Schemas\CorrecaoSubjetivaForm;
use App\Filament\Resources\CorrecaoSubjetivas\Tables\CorrecaoSubjetivasTable;
use App\Models\CorrecaoSubjetiva;
use App\Support\ConcursoAtual;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Entrada de correções da prova discursiva. O corretor identifica a folha pelo
 * código de controle (anônimo) — nunca pelo candidato (sigilo da correção).
 */
class CorrecaoSubjetivaResource extends Resource
{
    protected static ?string $model = CorrecaoSubjetiva::class;

    protected static ?string $modelLabel = 'Correção discursiva';
    protected static ?string $pluralModelLabel = 'Correções discursivas';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPencilSquare;

    protected static string | \UnitEnum | null $navigationGroup = 'Provas & Gabarito';

    protected static ?int $navigationSort = 21;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $cid = ConcursoAtual::id();
        if ($cid) {
            $query->whereHas('prova.inscricao', fn (Builder $q) => $q->where('concurso_id', $cid));
        }
        return $query;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return ConcursoAtual::id() !== null;
    }

    public static function form(Schema $schema): Schema
    {
        return CorrecaoSubjetivaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CorrecaoSubjetivasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCorrecaoSubjetivas::route('/'),
            'create' => CreateCorrecaoSubjetiva::route('/create'),
            'edit' => EditCorrecaoSubjetiva::route('/{record}/edit'),
        ];
    }
}
