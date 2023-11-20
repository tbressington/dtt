<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Client\Response;

interface StockPriceApiInterface
{
    /**
     * Get stock price for a given stock.
     * @param string $stock The stock name
     * @throws Exception
     * @return array
     */
    public function getStockPrice(string $stock): array;
}