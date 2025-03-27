<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Repositories\InMemorySupplierRepository;
use App\Services\PriceComparisonService;
use Illuminate\Http\JsonResponse;

/**
 * Handles price comparison requests and supplier selection.
 */
class PriceComparisonController extends Controller
{
    /**
     * The price comparison service instance.
     *
     * @var PriceComparisonService
     */
    private PriceComparisonService $priceComparisonService;

    /**
     * Create a new PriceComparisonController instance.
     *
     * @param InMemorySupplierRepository $repository
     */
    public function __construct(InMemorySupplierRepository $repository)
    {
        $this->priceComparisonService = new PriceComparisonService($repository);
    }

    /**
     * Find the best supplier based on the given order items.
     *
     * @param OrderRequest $request
     * @return JsonResponse
     */
    public function findCheapestSupplier(OrderRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $result = $this->priceComparisonService->findCheapestSupplier(
            array_column($validated['items'], 'quantity', 'product')
        );

        return response()->json($result);
    }
}
