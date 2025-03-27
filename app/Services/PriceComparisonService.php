<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Service for comparing product prices across different suppliers.
 */
class PriceComparisonService
{
    /**
     * Repository for retrieving suppliers.
     *
     * @var SupplierRepositoryInterface
     */
    private SupplierRepositoryInterface $supplierRepository;

    /**
     * Create a new PriceComparisonService instance.
     *
     * @param SupplierRepositoryInterface $supplierRepository
     */
    public function __construct(SupplierRepositoryInterface $supplierRepository)
    {
        $this->supplierRepository = $supplierRepository;
    }

    /**
     * Find supplier with the cheapest prices.
     *
     * @param array<string, int> $orderItems
     * @return array{bestSupplier: string|null, bestPrice: float}
     */
    public function findCheapestSupplier(array $orderItems): array
    {
        $suppliers = $this->supplierRepository->getAllSuppliers();

        $bestSupplier = null;
        $bestPrice = PHP_FLOAT_MAX;

        foreach ($suppliers as $supplier) {
            $totalCost = 0;

            foreach ($orderItems as $productName => $requiredQuantity) {
                $cost = $this->calculateCost($supplier->products, $productName, $requiredQuantity);
                if ($cost === null) {
                    $totalCost = PHP_FLOAT_MAX;
                    break;
                }
                $totalCost += $cost;
            }

            if ($totalCost < $bestPrice) {
                $bestPrice = $totalCost;
                $bestSupplier = $supplier->name;
            }
        }

        return [
            'bestSupplier' => $bestSupplier,
            'bestPrice' => $bestPrice
        ];
    }

    /**
     * Calculate cost for a supplier.
     *
     * @param Collection<Product> $products
     * @param string $productName
     * @param int $requiredQuantity
     * @return float|null
     */
    private function calculateCost(Collection $products, string $productName, int $requiredQuantity): ?float
    {
        $matchingProducts = $products->filter(
            fn($product) => $product->name === $productName
        )->sortByDesc('units');

        if ($matchingProducts->isEmpty()) {
            return null;
        }

        $remaining = $requiredQuantity;
        $totalCost = 0.0;

        foreach ($matchingProducts as $package) {
            while ($remaining >= $package->units) {
                $totalCost += $package->price;
                $remaining -= $package->units;
            }
        }

        // If there are leftovers, buy the smallest available package
        if ($remaining > 0) {
            foreach ($matchingProducts as $package) {
                if ($package->units >= $remaining) {
                    $totalCost += $package->price;
                    break;
                }
            }
        }

        return $totalCost;
    }
}
