<?php

namespace App\Repositories\Contracts;

use App\Models\Supplier;
use Illuminate\Support\Collection;

/**
 * Interface for Supplier repository.
 */
interface SupplierRepositoryInterface
{
    /**
     * Retrieve all suppliers.
     *
     * @return Collection<Supplier>
     */
    public function getAllSuppliers(): Collection;
}
