<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockPrices extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'stock_id',
        'current_price',
        'close_price',
    ];

    /**
     * Get the stock relating to the stock prices.
     */
    public function prices(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }
}
