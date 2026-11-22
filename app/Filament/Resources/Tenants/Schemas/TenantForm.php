<?php

namespace App\Filament\Resources\Tenants\Schemas;

use App\Filament\Resources\Tenants\TenantResource;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TenantForm
{
    public static function configure(Schema $schema): Schema
    {
        $plataforma = TenantResource::podeEditarPlataforma();

        return $schema->components([
            TextInput::make('nome')
                ->label('Nome da organizadora')
                ->required()
                ->maxLength(255),

            TextInput::make('slug')
                ->label('Subdomínio (slug)')
                ->helperText('Vira {slug}.certamme.com.br. Só a plataforma pode alterar.')
                ->required()
                ->alphaDash()
                ->unique(ignoreRecord: true)
                ->disabled(! $plataforma)
                ->dehydrated($plataforma),

            TextInput::make('dominio')
                ->label('Domínio próprio (opcional)')
                ->helperText('Ex.: concursos.prefeitura.gov.br (CNAME para a plataforma).')
                ->url()
                ->unique(ignoreRecord: true)
                ->disabled(! $plataforma)
                ->dehydrated($plataforma),

            ColorPicker::make('cor_primaria')
                ->label('Cor primária')
                ->helperText('Cor da marca no portal e nos botões. Vazio = padrão Certamme.'),

            FileUpload::make('logo_path')
                ->label('Logo')
                ->image()
                ->disk('public')
                ->directory('logos')
                ->visibility('public')
                ->imageEditor()
                ->maxSize(2048),

            Toggle::make('ativo')
                ->label('Ativa')
                ->default(true)
                ->disabled(! $plataforma)
                ->dehydrated($plataforma),
        ]);
    }
}
