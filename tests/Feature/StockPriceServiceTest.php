<?php

namespace Tests\Feature;

use App\Models\Share;
use App\Models\Stock;
use App\Models\StockPrices;
use App\Services\StockPriceApiFinnhub;
use App\Services\StockPriceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockPriceServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test calculating profit and loss for
     * no stock results in empty array
     */
    public function testCalculatingProfitAndLossForNoStockResultsInEmptyArray(): void
    {
        $stockPriceService = new StockPriceService(new StockPriceApiFinnhub());

        $result = $stockPriceService->calculateProfitAndLoss();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Test calculating profit and loss for stock with
     * no stock prices results in empty array
     */
    public function testCalculatingProfitAndLossForStockWithNoStockPricesResultsInEmptyArray(): void
    {
        Stock::create(['name' => 'AAPL']);

        $stockPriceService = new StockPriceService(new StockPriceApiFinnhub());

        $result = $stockPriceService->calculateProfitAndLoss();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Test calculating profit and loss for stock with
     * stock prices but no shares results in
     * empty array
     */
    public function testCalculatingProfitAndLossForStockWithStockPricesButNoSharesResultsInEmptyArray(): void
    {
        $stockAapl = Stock::create(['name' => 'AAPL']);
        $stockMsft = Stock::create(['name' => 'MSFT']);

        StockPrices::create([
            'stock_id' => $stockAapl->id,
            'current_price' => 111.22,
            'close_price' => 111.12
        ]);
        StockPrices::create([
            'stock_id' => $stockMsft->id,
            'current_price' => 222.33,
            'close_price' => 222.23
        ]);

        $stockPriceService = new StockPriceService(new StockPriceApiFinnhub());

        $result = $stockPriceService->calculateProfitAndLoss();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Test calculating profit and loss for shares in single stock
     * with existing stock and prices returns expected result
     */
    public function testCalculatingProfitAndLossForSharesInSingleStockWithExistingStockAndPricesReturnsExpectedResult(): void
    {
        $stockAapl = Stock::create(['name' => 'AAPL']);
        $stockMsft = Stock::create(['name' => 'MSFT']);

        StockPrices::create([
            'stock_id' => $stockAapl->id,
            'current_price' => 111.22,
            'close_price' => 111.12
        ]);
        StockPrices::create([
            'stock_id' => $stockMsft->id,
            'current_price' => 222.33,
            'close_price' => 222.23
        ]);

        $shares = Share::create([
            'user_id' => 1,
            'stock_id' => $stockAapl->id,
            'quantity' => 5
        ]);
        $sharesCreated = $shares->created_at;

        $stockPriceService = new StockPriceService(new StockPriceApiFinnhub());

        $expected = [
            'AAPL' => [
                'single_share_pl' => 0.10,
                'total_shares_pl' => 0.50,
                'created' => $sharesCreated
            ]
        ];
        $result = $stockPriceService->calculateProfitAndLoss();

        $this->assertEquals($expected, $result);
    }

    /**
     * Test calculating profit and loss for shares in both AAPL and MSFT
     * stock with existing stock and prices returns expected result
     */
    public function testCalculatingProfitAndLossForSharesInBothStockWithExistingStockAndPricesReturnsExpectedResult(): void
    {
        $stockAAPL = Stock::create(['name' => 'AAPL']);
        $stockMSFT = Stock::create(['name' => 'MSFT']);

        StockPrices::create([
            'stock_id' => $stockAAPL->id,
            'current_price' => 111.22,
            'close_price' => 111.12
        ]);
        StockPrices::create([
            'stock_id' => $stockMSFT->id,
            'current_price' => 222.33,
            'close_price' => 223.78
        ]);

        $sharesAAPL = Share::create([
            'user_id' => 1,
            'stock_id' => $stockAAPL->id,
            'quantity' => 10
        ]);
        $sharesMSFT = Share::create([
            'user_id' => 1,
            'stock_id' => $stockMSFT->id,
            'quantity' => 10
        ]);
        $sharesAAPLCreated = $sharesAAPL->created_at;
        $sharesMSFTCreated = $sharesMSFT->created_at;

        $stockPriceService = new StockPriceService(new StockPriceApiFinnhub());

        $expected = [
            'AAPL' => [
                'single_share_pl' => 0.10,
                'total_shares_pl' => 1.00,
                'created' => $sharesAAPLCreated
            ],
            'MSFT' => [
                'single_share_pl' => -1.45,
                'total_shares_pl' => -14.5,
                'created' => $sharesAAPLCreated
            ]
        ];
        $result = $stockPriceService->calculateProfitAndLoss();

        $this->assertEquals($expected, $result);
    }
}
