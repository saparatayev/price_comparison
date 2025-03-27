<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Repositories\InMemorySupplierRepository;
use App\Services\PriceComparisonService;
use Illuminate\Http\JsonResponse;

class PriceComparisonController extends Controller
{
    private PriceComparisonService $priceComparisonService;

    public function __construct(InMemorySupplierRepository $repository)
    {
        $this->priceComparisonService = new PriceComparisonService($repository);
    }

    public function findCheapestSupplier(OrderRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $result = $this->priceComparisonService->findCheapestSupplier(
            array_column($validated['items'], 'quantity', 'product')
        );

        return response()->json($result);
    }
}
