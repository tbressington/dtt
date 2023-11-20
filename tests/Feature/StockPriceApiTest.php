<?php

namespace Tests\Feature;

use App\Services\StockPriceApiFinnhub;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class StockPriceApiTest extends TestCase
{
    /**
     * Test missing API key throws exception.
     */
    public function testMissingApiKeyThrowsException(): void
    {
        $stock = 'AAPL';

        Env::getRepository()->set('FINNHUB_API_KEY', 'null');
        $apiKeyActual = env('FINNHUB_API_KEY');
        $this->assertNull($apiKeyActual);

        $this->expectException('Exception');
        $this->expectExceptionMessage('Missing API key');
        $stockPriceApi = app(StockPriceApiFinnhub::class);
        $stockPriceApi->getStockPrice($stock);
    }

    /**
     * Test unsuccessful response from API throws exception.
     */
    public function testUnsuccessfulApiResponseThrowsException(): void
    {
        $stock = 'AAPL';

        Env::getRepository()->set('FINNHUB_API_KEY', 'testapikey');

        Http::fake([
            'https://finnhub.io/api/*' => Http::response([], 500)
        ]);
        
        $this->expectException('Exception');
        $this->expectExceptionMessage('Failed to get stock prices for ('.$stock.')');
        $stockPriceApi = app(StockPriceApiFinnhub::class);
        $response = $stockPriceApi->getStockPrice($stock);
    }

    /**
     * Test missing expected field in JSON response throws exception.
     */
    public function testMissingExpectedFieldInJsonResponseThrowsException(): void
    {
        $stock = 'AAPL';

        Env::getRepository()->set('FINNHUB_API_KEY', 'testapikey');

        Http::fake([
            'https://finnhub.io/api/*' => Http::response([], 200)
        ]);
        
        $this->expectException('Exception');
        $this->expectExceptionMessage('Either JSON is invalid, or "current_price" and / or "previous_close_price" missing for ('.$stock.')');
        $stockPriceApi = app(StockPriceApiFinnhub::class);
        $response = $stockPriceApi->getStockPrice($stock);
    }

    /**
     * Test response contains valid JSON.
     */
    public function testResponseContainsValidJson(): void
    {
        $stock = 'AAPL';

        Env::getRepository()->set('FINNHUB_API_KEY', 'testapikey');

        Http::fake([
            'https://finnhub.io/api/*' => Http::response([
                'c' => 123.45,
                'pc' => 123.45
            ], 200)
        ]);
        
        $stockPriceApi = app(StockPriceApiFinnhub::class);
        $response = $stockPriceApi->getStockPrice($stock);

        $this->assertIsArray($response);
    }
}
