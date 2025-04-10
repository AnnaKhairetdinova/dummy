<?php

namespace App\DTO;

class ProductData
{
    public int $external_id;
    public string $title;
    public ?string $description = null;
    public float $price;
    public ?float $rating = null;
    public ?string $brand = null;
    public ?string $category = null;
    public array $images = [];

    public static function fromArray(array $data): self
    {
        $productData = new self();

        $productData->external_id = $data['id'] ?? null;
        $productData->title = $data['title'] ?? '';
        $productData->description = $data['description'] ?? null;
        $productData->price = isset($data['price']) ? (float) $data['price'] : 0.0;
        $productData->rating = isset($data['rating']) ? (float) $data['rating'] : null;
        $productData->brand = $data['brand'] ?? null;
        $productData->category = $data['category'] ?? null;
        $productData->images = $data['images'] ?? [];

        return $productData;
    }

    public function toDB(): array
    {
        return [
            'external_id' => $this->external_id,
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'rating' => $this->rating,
            'brand' => $this->brand,
            'category' => $this->category,
            'images' => $this->images,
        ];
    }

    public function toApi(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'rating' => $this->rating,
            'brand' => $this->brand,
            'category' => $this->category,
            'images' => $this->images,
        ];
    }
}
