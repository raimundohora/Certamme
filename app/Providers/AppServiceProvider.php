<?php

namespace App\Providers;

use App\Models\Isencao;
use App\Models\Recurso;
use App\Observers\IsencaoObserver;
use App\Observers\RecursoObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Isencao::observe(IsencaoObserver::class);
        Recurso::observe(RecursoObserver::class);
    }
}
