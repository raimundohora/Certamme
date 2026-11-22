<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = ['nome', 'slug', 'dominio', 'ativo'];
    protected $casts = ['ativo' => 'boolean'];

    protected static ?Tenant $current = null;
    public static function current(): ?Tenant { return static::$current; }
    public static function setCurrent(?Tenant $tenant): void { static::$current = $tenant; }
}
