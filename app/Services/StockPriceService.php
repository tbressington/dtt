<?php

namespace App\Services;

use App\Models\Share;
use App\Models\Stock;
use App\Models\StockPrices;
use Exception;
use Illuminate\Support\Facades\Log;

class StockPriceService
{
    public function __construct(private readonly StockPriceApiInterface $stockPriceApi)
    {}

    /**
     * Get the stock prices from the StockPriceApi.
     * @return array
     */
    public function getStockPrices(string $stock): array
    {
        try {
            $stockPrice = $this->stockPriceApi->getStockPrice($stock);

            return [
                'current_price' => round($stockPrice['current_price'], 2, PHP_ROUND_HALF_UP),
                'previous_close_price' => round($stockPrice['previous_close_price'], 2 , PHP_ROUND_HALF_UP),
            ];
        } catch (Exception $e) {
            Log::error(__METHOD__ . $e->getMessage());
            return [];
        }
    }

    /**
     * @inheritdoc
     */
    public function calculateProfitAndLoss(): array
    {
        $profitAndLoss = [];

        foreach (Stock::all() as $stock) {
            // Get the latest stock price data from the database.
            $latestStockPrice = StockPrices::where('stock_id', $stock->id)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$latestStockPrice instanceof StockPrices) {
                continue;
            }

            $shares = Share::where('stock_id', $stock->id)->first();
            if (!$shares instanceof Share || $shares->quantity < 1) {
                continue;
            }

            $stockProfitAndLossSingle = number_format(
                round($latestStockPrice->current_price, 2, PHP_ROUND_HALF_UP)
                - round($latestStockPrice->close_price, 2, PHP_ROUND_HALF_UP),
                2
            );

            $stockProfitAndLossAll = number_format(
                round($shares->quantity * $stockProfitAndLossSingle, 2, PHP_ROUND_HALF_UP),
                2
            );

            $profitAndLoss[$stock->name] = [
                'single_share_pl' => $stockProfitAndLossSingle,
                'total_shares_pl' => $stockProfitAndLossAll,
                'created' => $latestStockPrice->created_at,
            ];
        }

        return $profitAndLoss;
    }
}