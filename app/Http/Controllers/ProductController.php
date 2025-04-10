<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductController
{
    protected ProductService $service;

    protected array $validationRules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'price' => 'required|numeric|min:0|max:10000',
        'brand' => 'required|string|max:255',
        'category' => 'required|string|max:255',
        'rating' => 'nullable|numeric|min:0|max:5',
        'images' => 'nullable|array|max:10',
        'images.*' => 'nullable|url',
    ];

    public function __construct(ProductService $productService)
    {
        $this->service = $productService;
    }

    /**
     * Получение товаров из бд с пагинацией
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function productList(Request $request): JsonResponse
    {
        try {
            $limit = (int) $request->query('limit', 30);
            $page = (int) $request->query('page', 1);

            $products = $this->service->getPaginatedProducts($limit, $page);

            return response()->json([
                'list' => $products->items(),
                'pagination' => [
                    'total' => $products->total(),
                    'page_size' => $products->perPage(),
                    'current_page' => $products->currentPage(),
                ]
            ]);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Добавление товара в dummy
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function addProduct(Request $request): JsonResponse
    {
        try {
            $data = $request->validate($this->validationRules);

            $product = $this->service->addProduct($data);

            return response()->json($product);
        } catch (ValidationException $e) {
            return response()->json($e->errors(), 400);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
