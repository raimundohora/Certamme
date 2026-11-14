<?php

namespace App\Filament\Resources\Anexos\Schemas;

use App\Enums\GrupoAnexo;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AnexoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('concurso_id')
                    ->label('Concurso')
                    ->relationship('concurso', 'nome')
                    ->searchable()
                    ->required(),
                TextInput::make('titulo')
                    ->label('Título')
                    ->required(),
                Select::make('grupo')
                    ->label('Grupo')
                    ->options(collect(GrupoAnexo::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()]))
                    ->default('edital')
                    ->required(),
                FileUpload::make('arquivo')
                    ->label('Arquivo (PDF)')
                    ->disk('public')
                    ->directory('anexos')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(20480),
                Toggle::make('publicado')
                    ->label('Publicado')
                    ->default(true),
            ]);
    }
}
