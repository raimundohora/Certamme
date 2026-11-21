<?php

namespace App\Filament\Resources\Candidatos\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CandidatoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nome')
                    ->label('Nome completo')
                    ->required()
                    ->maxLength(255),
                TextInput::make('cpf')
                    ->label('CPF')
                    ->required()
                    ->maxLength(14),
                TextInput::make('nome_social')
                    ->label('Nome social')
                    ->maxLength(255),
                TextInput::make('nome_mae')
                    ->label('Nome da mãe')
                    ->maxLength(255),
                DatePicker::make('data_nascimento')
                    ->label('Data de nascimento'),
                Select::make('sexo')
                    ->label('Sexo')
                    ->options([
                        'M' => 'Masculino',
                        'F' => 'Feminino',
                        'O' => 'Outro',
                    ]),
                TextInput::make('rg')
                    ->label('RG')
                    ->maxLength(20),
                TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->maxLength(255),
                TextInput::make('telefone')
                    ->label('Telefone')
                    ->tel()
                    ->maxLength(20),
                TextInput::make('cep')
                    ->label('CEP')
                    ->maxLength(9),
                TextInput::make('logradouro')
                    ->label('Logradouro')
                    ->maxLength(255),
                TextInput::make('numero')
                    ->label('Número')
                    ->maxLength(20),
                TextInput::make('bairro')
                    ->label('Bairro')
                    ->maxLength(255),
                TextInput::make('cidade')
                    ->label('Cidade')
                    ->maxLength(255),
                TextInput::make('uf')
                    ->label('UF')
                    ->maxLength(2),
                Toggle::make('pcd')
                    ->label('PcD (pessoa com deficiência)'),
                TextInput::make('tipo_deficiencia')
                    ->label('Tipo de deficiência')
                    ->maxLength(255),
                TextInput::make('password')
                    ->label('Senha (opcional)')
                    ->password()
                    ->dehydrated(fn ($state) => filled($state))
                    ->maxLength(255),
            ]);
    }
}
