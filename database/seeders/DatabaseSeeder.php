<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $stockAAPL = \App\Models\Stock::factory()->create([
            'name' => 'AAPL'
        ]);

        $stockMSFT = \App\Models\Stock::factory()->create([
            'name' => 'MSFT'
        ]);

        $user = \App\Models\User::factory()->create([
            'name' => 'Delio User',
            'email' => 'delio@example.com',
            'active' => true,
        ]);

        \App\Models\Share::factory()->create([
            'user_id' => $user->id,
            'stock_id' => $stockAAPL->id,
            'quantity' => 10,
        ]);

        \App\Models\Share::factory()->create([
            'user_id' => $user->id,
            'stock_id' => $stockMSFT->id,
            'quantity' => 10,
        ]);

        \App\Models\StockPrices::factory()->create([
            'stock_id' => $stockAAPL->id,
            'current_price' => 0,
            'close_price' => 0
        ]);
        \App\Models\StockPrices::factory()->create([
            'stock_id' => $stockMSFT->id,
            'current_price' => 0,
            'close_price' => 0
        ]);
    }
}
