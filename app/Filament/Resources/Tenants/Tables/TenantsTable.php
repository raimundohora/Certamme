<?php

namespace App\Filament\Resources\Tenants\Tables;

use App\Filament\Resources\Tenants\TenantResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TenantsTable
{
    public static function configure(Table $table): Table
    {
        $plataforma = TenantResource::podeEditarPlataforma();

        return $table
            ->columns([
                ImageColumn::make('logo_path')
                    ->label('Logo')
                    ->disk('public')
                    ->height(28),
                TextColumn::make('nome')
                    ->label('Nome')
                    ->searchable(),
                TextColumn::make('slug')
                    ->label('Subdomínio')
                    ->searchable(),
                TextColumn::make('dominio')
                    ->label('Domínio próprio')
                    ->placeholder('—')
                    ->toggleable(),
                ColorColumn::make('cor_primaria')
                    ->label('Cor'),
                IconColumn::make('ativo')
                    ->label('Ativa')
                    ->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions(
                $plataforma
                    ? [BulkActionGroup::make([DeleteBulkAction::make()])]
                    : []
            );
    }
}
