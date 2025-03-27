# Project Structure

You can check the files in this project's structure below.


```
app/
│── Models/
│   │── Product.php
│   └── Supplier.php
│── Repositories/
│   │── Contracts/
│   │   └── SupplierRepositoryInterface.php
│   └── InMemorySupplierRepository.php
│── Services/
│   └── PriceComparisonService.php
database/
│── migrations/
│   │── 2025_03_27_201803_create_suppliers_table.php
│   └── 2025_03_27_201813_create_products_table.php
tests/
│── Unit/
│   └── PriceComparisonServiceTest.php
routes/
└── api.php
```

To run the tests, use the following command:
```bash
php artisan test --env=testing --filter=PriceComparisonServiceTest
```
