<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository
{
    protected Product $model;

    public function __construct()
    {
        $this->model = new Product();
    }

    /**
     * Список товаров с пагинацией
     *
     * @param int $limit
     * @param int $page
     * @param array $columns
     * @return LengthAwarePaginator
     */
    public function paginate(int $limit = 30, int $page = 1, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->paginate($limit, $columns, 'page', $page);
    }

    /**
     * Добавление товара в бд
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Получение по названии колонки и значении
     *
     * @param string $field
     * @param mixed $value
     * @return Model|null
     */
    public function findByField(string $field, mixed $value): ?Model
    {
        return $this->model->where($field, $value)->first();
    }

    /**
     * Получение товара по внешнему идентификатору
     *
     * @param int $externalId
     * @return Model|Product|null
     */
    public function findByExternalId(int $externalId): Model|Product|null
    {
        return $this->findByField('external_id', $externalId);
    }
}
