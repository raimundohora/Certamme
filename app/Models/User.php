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
        // (o subdomínio em uso). Impede sessão de uma organizadora valer em outra.
        return Tenant::current() !== null && $this->tenant_id === Tenant::current()->id;
    }
}
