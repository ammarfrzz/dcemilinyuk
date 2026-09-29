<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class DcemilinyukServiceProvider extends ServiceProvider
{
    /**
     * Register konfigurasi brand DcemilinYuk.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/dcemilinyuk.php', 'dcemilinyuk');
    }

    public function boot(): void
    {
        //
    }
}
