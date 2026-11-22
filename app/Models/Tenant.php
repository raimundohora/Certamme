<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Tenant extends Model
{
    protected $fillable = ['nome', 'slug', 'dominio', 'cor_primaria', 'logo_path', 'ativo'];
    protected $casts = ['ativo' => 'boolean'];

    protected static ?Tenant $current = null;
    public static function current(): ?Tenant { return static::$current; }
    public static function setCurrent(?Tenant $tenant): void { static::$current = $tenant; }

    /** Cor da marca (hex). Fallback: verde esmeralda do Certamme. */
    public function corPrimaria(): string
    {
        return $this->cor_primaria ?: '#059669';
    }

    /** URL pública do logo, ou null. */
    public function logoUrl(): ?string
    {
        if (! $this->logo_path) {
            return null;
        }
        return Storage::disk('public')->url($this->logo_path);
    }
}
