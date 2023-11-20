<?php

namespace App\Providers;

use App\Services\StockPriceApiFinnhub;
use App\Services\StockPriceApiInterface;
use Illuminate\Support\ServiceProvider;

class StockPricesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->bind(StockPriceApiInterface::class, StockPriceApiFinnhub::class);
    }
}
