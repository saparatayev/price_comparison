<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use App\Services\PriceComparisonService;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use Mockery;

class PriceComparisonServiceTest extends TestCase
{
    private SupplierRepositoryInterface $mockRepository;
    private PriceComparisonService $priceComparisonService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockRepository = Mockery::mock(SupplierRepositoryInterface::class);
        $this->priceComparisonService = new PriceComparisonService($this->mockRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function createMockProduct(string $name, int $units, float $price)
    {
        $mockProduct = Mockery::mock(Product::class);

        $mockProduct->shouldReceive('getAttribute')
            ->with('name')->andReturn($name);
        $mockProduct->shouldReceive('getAttribute')
            ->with('units')->andReturn($units);
        $mockProduct->shouldReceive('getAttribute')
            ->with('price')->andReturn($price);
        $mockProduct->shouldReceive('setAttribute')->with('name', $name);
        $mockProduct->shouldReceive('setAttribute')->with('units', $units);
        $mockProduct->shouldReceive('setAttribute')->with('price', $price);
        $mockProduct->allows()->offsetExists(Mockery::any())->andReturn(true);
        $mockProduct->allows()->offsetGet('units')->andReturn($units);

        $mockProduct->name = $name;
        $mockProduct->units = $units;
        $mockProduct->price = $price;

        return $mockProduct;
    }

    private function createMockSupplier(string $name, Collection $products)
    {
        $mockSupplier = Mockery::mock(Supplier::class);

        $mockSupplier->shouldReceive('getAttribute')
            ->with('name')->andReturn($name);

        $mockSupplier->shouldReceive('getAttribute')
            ->with('products')->andReturn($products);
        $mockSupplier->shouldReceive('setAttribute')->with('name', $name);
        $mockSupplier->shouldReceive('setAttribute')->with('products', $products);


        $mockSupplier->name = $name;
        $mockSupplier->products = $products;

        return $mockSupplier;
    }

    private function createMockSuppliersWithProducts(): array
    {
        // Create mock products for Supplier A
        $productsA = collect([
            $this->createMockProduct('Dental Floss', 1, 9.00),
            $this->createMockProduct('Dental Floss', 20, 160.00),
            $this->createMockProduct('Ibuprofen', 1, 5.00),
            $this->createMockProduct('Ibuprofen', 10, 48.00),
            $this->createMockProduct('Ibuprofen', 100, 480.00)
        ]);

        // Create mock products for Supplier B
        $productsB = collect([
            $this->createMockProduct('Dental Floss', 1, 8.00),
            $this->createMockProduct('Dental Floss', 10, 71.00),
            $this->createMockProduct('Ibuprofen', 1, 6.00),
            $this->createMockProduct('Ibuprofen', 5, 25.00),
            $this->createMockProduct('Ibuprofen', 100, 410.00)
        ]);

        // Create mock suppliers
        $supplierA = $this->createMockSupplier('Supplier A', $productsA);
        $supplierB = $this->createMockSupplier('Supplier B', $productsB);

        return [$supplierA, $supplierB];
    }

    public function testFindCheapestSupplierForDentalFlossAndIbuprofen()
    {
        // Return suppliers from mocked repository
        $this->mockRepository->shouldReceive('getAllSuppliers')
            ->andReturn(collect($this->createMockSuppliersWithProducts()));

        $orderItems = [
            'Dental Floss' => 5,
            'Ibuprofen' => 12
        ];

        $result = $this->priceComparisonService->findCheapestSupplier($orderItems);

        $this->assertNotNull($result);
        $this->assertEquals('Supplier B', $result['bestSupplier']);
        $this->assertIsFloat($result['bestPrice']);
        $this->assertEquals(102.00, $result['bestPrice'], 'Best price should be 102.00');

        $this->mockRepository->shouldHaveReceived('getAllSuppliers')->once();
    }

    public function testFindCheapestSupplierForIbuprofen()
    {
        // Return suppliers from mocked repository
        $this->mockRepository->shouldReceive('getAllSuppliers')
            ->andReturn(collect($this->createMockSuppliersWithProducts()));

        $orderItems = [
            'Ibuprofen' => 105
        ];

        $result = $this->priceComparisonService->findCheapestSupplier($orderItems);

        $this->assertNotNull($result);
        $this->assertEquals('Supplier B', $result['bestSupplier']);
        $this->assertIsFloat($result['bestPrice']);
        $this->assertEquals(435.00, $result['bestPrice'], 'Best price should be 435.00');

        $this->mockRepository->shouldHaveReceived('getAllSuppliers')->once();
    }
}
