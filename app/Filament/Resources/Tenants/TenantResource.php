<?php

namespace App\Filament\Resources\Tenants;

use App\Filament\Resources\Tenants\Pages\CreateTenant;
use App\Filament\Resources\Tenants\Pages\EditTenant;
use App\Filament\Resources\Tenants\Pages\ListTenants;
use App\Filament\Resources\Tenants\Schemas\TenantForm;
use App\Filament\Resources\Tenants\Tables\TenantsTable;
use App\Models\Tenant;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

/**
 * Organizadoras (tenants). Super-admin da plataforma (users.tenant_id NULL) vê e
 * gerencia TODAS; admin de organizadora vê e edita só a PRÓPRIA (identidade visual).
 */
class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $modelLabel = 'Organizadora';
    protected static ?string $pluralModelLabel = 'Organizadoras';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;
    protected static string|\UnitEnum|null $navigationGroup = 'Sistema';
    protected static ?int $navigationSort = 1;

    protected static function ehPlataforma(): bool
    {
        $u = auth()->user();
        return $u !== null && method_exists($u, 'isPlataforma') && $u->isPlataforma();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // admin de organizadora enxerga só a própria; super-admin vê todas
        if (! static::ehPlataforma()) {
            $query->whereKey(Tenant::current()?->id ?? 0);
        }

        return $query;
    }

    /** Só o super-admin da plataforma cria/apaga organizadoras. */
    public static function canCreate(): bool
    {
        return static::ehPlataforma();
    }

    /**
     * No Filament v4 a página de criação chama authorizeCreate() (via policy),
     * não canCreate(). Sem TenantPolicy, o Filament libera por padrão — então
     * fechamos aqui o acesso por URL à criação para quem não é plataforma.
     */
    public static function authorizeCreate(): void
    {
        abort_unless(static::ehPlataforma(), 403);
    }

    public static function canDelete($record): bool
    {
        return static::ehPlataforma();
    }

    public static function canDeleteAny(): bool
    {
        return static::ehPlataforma();
    }

    public static function podeEditarPlataforma(): bool
    {
        return static::ehPlataforma();
    }

    public static function form(Schema $schema): Schema
    {
        return TenantForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TenantsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTenants::route('/'),
            'create' => CreateTenant::route('/create'),
            'edit'   => EditTenant::route('/{record}/edit'),
        ];
    }
}
