<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class StockPriceApiFinnhub implements StockPriceApiInterface
{
    private const API_URL = 'https://finnhub.io/api/v1/quote?';

    private const DATA_MAP = [
        'current_price' => 'c',
        'previous_close_price' => 'pc',
    ];

    private ?string $apiKey;

    public function __construct()
    {
        $this->apiKey = env('FINNHUB_API_KEY', null);
    }

    /**
     * @inheritdoc
     */
    public function getStockPrice(string $stock): array
    {
        $stock = strtoupper($stock);

        if (!$this->apiKey) {
            throw new Exception('Missing API key');
        }

        $response = $this->getStockPricesFromFinnhub($stock);

        if (!$response->successful()) {
            throw new Exception('Failed to get stock prices for (' . $stock . ')');
        }

        if (
            !is_array($response->json())
            || !array_key_exists(self::DATA_MAP['current_price'], $response->json())
            || !array_key_exists(self::DATA_MAP['previous_close_price'], $response->json())
        ) {
            throw new Exception('Either JSON is invalid, or "current_price" and / or "previous_close_price" missing for (' . $stock . ')');
        }

        $json = $response->json();

        return [
            'current_price' => $json[self::DATA_MAP['current_price']],
            'previous_close_price' => $json[self::DATA_MAP['previous_close_price']],
        ];
    }

    /**
     * Makes a call to the Finnhub API and returns the response.
     * @param string $stock
     * @return Response
     */
    public function getStockPricesFromFinnhub(string $stock): Response
    {
        return Http::get(self::API_URL . 'symbol=' . $stock . '&token=' . $this->apiKey);
    }
}