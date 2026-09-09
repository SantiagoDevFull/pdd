<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class FpdfServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('Fpdf', function () {
            return new \FPDF();
        });
    }

    public function boot()
    {
    }
}
