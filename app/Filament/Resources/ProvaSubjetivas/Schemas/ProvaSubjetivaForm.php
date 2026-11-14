<?php

namespace App\Filament\Resources\ProvaSubjetivas\Schemas;

use App\Enums\StatusSubjetiva;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProvaSubjetivaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'nome')
                    ->hidden(),
            ]);
    }
}
