<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService
{
    public function __construct(
        protected DummyJsonService $dummyJsonService,
        protected ProductRepository $productRepository
    ) {
    }

    /**
     * Получение списка товаров с пагинацией
     *
     * @param int $limit
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function getPaginatedProducts(int $limit = 30, int $page = 1): LengthAwarePaginator
    {
        return $this->productRepository->paginate($limit, $page);
    }

    /**
     * Добавление товара в dummy
     *
     * @param array $data
     * @return array
     * @throws ConnectionException
     * @throws Exception
     */
    public function addProduct(array $data): array
    {
        $result = $this->dummyJsonService->addProduct($data);

        if (!$result) {
            throw new Exception('Ошибка при добавление товара');
        }

        return $result;
    }
}
