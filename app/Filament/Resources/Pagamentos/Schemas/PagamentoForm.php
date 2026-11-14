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
