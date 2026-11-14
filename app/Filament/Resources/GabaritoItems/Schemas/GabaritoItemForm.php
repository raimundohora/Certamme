<?php

namespace App\Filament\Resources\GabaritoItems\Schemas;

use App\Enums\SituacaoQuestao;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GabaritoItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'nome')
                    ->hidden(),
                TextInput::make('numero')
                    ->required()
                    ->numeric(),
                TextInput::make('disciplina'),
                TextInput::make('resposta_correta'),
                Select::make('situacao')
                    ->options(SituacaoQuestao::class)
                    ->default('valida')
                    ->required(),
                TextInput::make('resposta_nova'),
                TextInput::make('peso')
                    ->required()
                    ->numeric()
                    ->default(1),
            ]);
    }
}
