<?php

namespace App\Console\Commands;

use App\Models\Stock;
use App\Models\StockPrices;
use App\Services\StockPriceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GetStockPricesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delio:get-stock-prices-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get latest stock prices from the Stock Prices API';

    /**
     * Execute the console command.
     * @param StockPriceService $stockPriceService
     */
    public function handle(StockPriceService $stockPriceService)
    {
        // Get the name for all stock EFTs so we can
        // query the Stock Prices API.
        foreach (Stock::all() as $stock) {
            $stockPrice = $stockPriceService->getStockPrices($stock->name);
            
            if (!isset($stockPrice['current_price'], $stockPrice['previous_close_price'])) {
                Log::error('Unable to add stock price for ('.$stock->name.').');
                continue;
            }

            $newStockPrice = [
                'stock_id' => $stock->id,
                'current_price' => $stockPrice['current_price'],
                'close_price' => $stockPrice['previous_close_price'],
            ];

            Log::info('Added stock price ('.$stock->name.') --> '.json_encode($newStockPrice));

            StockPrices::create($newStockPrice);
        }
    }
}
