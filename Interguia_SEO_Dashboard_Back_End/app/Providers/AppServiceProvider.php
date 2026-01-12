<?php

namespace App\Providers;

use App\Helpers\SapDatabaseHelper;
use Illuminate\Support\ServiceProvider;
use App\Helpers\SeoDatabaseHelper;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        SeoDatabaseHelper::loadSavedConnections(); // Al iniciar la app esto corre y verifica si hay credenciales guardadas y conecta si es asi
        SapDatabaseHelper::loadSavedConnections();
         // Al iniciar la app esto corre y verifica si hay credenciales guardadas y conecta si es asi
    }
}
