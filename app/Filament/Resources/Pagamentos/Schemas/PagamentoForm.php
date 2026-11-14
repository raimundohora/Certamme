<?php

namespace App\Filament\Resources\Pagamentos\Schemas;

use App\Enums\MetodoPagamento;
use App\Enums\StatusPagamento;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PagamentoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'nome')
                    ->hidden(),
                Select::make('inscricao_id')
                    ->relationship('inscricao', 'id')
                    ->required(),
                TextInput::make('valor')
                    ->required()
                    ->numeric()
                    ->default(0),
                Select::make('metodo')
                    ->options(MetodoPagamento::class)
                    ->default('pix')
                    ->required(),
                Select::make('status')
                    ->options(StatusPagamento::class)
                    ->default('pendente')
                    ->required(),
                TextInput::make('external_id'),
                TextInput::make('secure_url')
                    ->url(),
                TextInput::make('linha_digitavel'),
                Textarea::make('pix_qrcode')
                    ->columnSpanFull(),
                DateTimePicker::make('pago_em'),
            ]);
    }
}
