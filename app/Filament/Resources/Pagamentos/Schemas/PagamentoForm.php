<?php

namespace App\Filament\Resources\Pagamentos\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PagamentoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('inscricao_id')->label('Inscricao id')->relationship('inscricao', 'nome')->searchable()->required(),
                TextInput::make('valor')->label('Valor')->numeric(),
                TextInput::make('metodo')->label('Metodo')->maxLength(255),
                TextInput::make('status')->label('Status')->maxLength(255),
                TextInput::make('external_id')->label('External id')->numeric(),
                TextInput::make('secure_url')->label('Secure url')->maxLength(255),
                TextInput::make('linha_digitavel')->label('Linha digitavel')->maxLength(255),
                TextInput::make('pix_qrcode')->label('Pix qrcode')->maxLength(255),
                DatePicker::make('pago_em')->label('Pago em'),
            ]);
    }
}
