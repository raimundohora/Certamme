<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = ['tenant_id', 'name', 'email', 'password'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** Super-admin da plataforma: sem organizadora, opera qualquer subdomínio. */
    public function isPlataforma(): bool
    {
        return $this->tenant_id === null;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // Super-admin da plataforma acessa o admin de qualquer organizadora.
        if ($this->isPlataforma()) {
            return true;
        }

        // Admin de organizadora só acessa o admin da PRÓPRIA organizadora
        // (o subdomínio/host em uso). Impede sessão de uma organizadora valer em outra.
        // Resolve pelo host diretamente: o Authenticate do Filament pode rodar antes do
        // SetPortalTenant (prioridade de middleware), deixando Tenant::current() null.
        $atual = Tenant::current() ?? Tenant::resolveByHost(request()->getHost());

        return $atual !== null && $this->tenant_id === $atual->id;
    }
}
