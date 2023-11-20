<?php

namespace App\Http\Controllers;

use App\Services\StockPriceService;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    public function __construct(private readonly StockPriceService $stockPriceService)
    {}

    /**
     * Display a listing of the resource.
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return new JsonResponse($this->stockPriceService->calculateProfitAndLoss());
    }
}
