<?php

namespace App\Repositories;

use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * An in-memory implementation of the SupplierRepositoryInterface.
 */
class InMemorySupplierRepository implements SupplierRepositoryInterface
{
    /**
     * Retrieve all suppliers.
     *
     * @return Collection<Supplier>
     */
    public function getAllSuppliers(): Collection
    {
        // Create suppliers dynamically
        $supplierA = Supplier::create(['name' => 'Supplier A']);
        $supplierB = Supplier::create(['name' => 'Supplier B']);
        // Create products for Supplier A
        $supplierA->products()->createMany([
            ['name' => 'Dental Floss', 'units' => 1, 'price' => 9.00],
            ['name' => 'Dental Floss', 'units' => 20, 'price' => 160.00],
            ['name' => 'Ibuprofen', 'units' => 1, 'price' => 5.00],
            ['name' => 'Ibuprofen', 'units' => 10, 'price' => 48.00]
        ]);
        // Create products for Supplier B
        $supplierB->products()->createMany([
            ['name' => 'Dental Floss', 'units' => 1, 'price' => 8.00],
            ['name' => 'Dental Floss', 'units' => 10, 'price' => 71.00],
            ['name' => 'Ibuprofen', 'units' => 1, 'price' => 6.00],
            ['name' => 'Ibuprofen', 'units' => 5, 'price' => 25.00],
            ['name' => 'Ibuprofen', 'units' => 100, 'price' => 410.00]
        ]);
        return collect([$supplierA, $supplierB]);
    }
}
