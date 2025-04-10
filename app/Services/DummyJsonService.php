<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class DummyJsonService
{
    protected mixed $baseUrl;
    protected ?string $authToken = null;

    public function __construct()
    {
        $this->baseUrl = Config::get('api.base_url');
        $this->login();
    }

    /**
     * Авторизация
     *
     * @return void
     */
    protected function login(): void
    {
        $username = Config::get('api.auth.username');
        $password = Config::get('api.auth.password');

        if ($username && $password) {
            $response = Http::post("{$this->baseUrl}/auth/login", [
                'username' => $username,
                'password' => $password,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->authToken = $data['token'];
            }
        }
    }

    /**
     * @return PendingRequest
     */
    protected function getHttpClient(): PendingRequest
    {
        $client = Http::acceptJson();

        if ($this->authToken) {
            $client = $client->withHeaders([
                'Authorization' => "Bearer {$this->authToken}",
            ]);
        }

        return $client;
    }

    /**
     * Добавление товара
     *
     * @param array $data
     * @return array
     * @throws ConnectionException
     * @throws Exception
     */
    public function addProduct(array $data): array
    {
        $response = $this->getHttpClient()->post("{$this->baseUrl}/products/add", $data);

        if (!$response->successful()) {
            throw new Exception('Ошибка при добавление товара');
        }

        return $response->json();
    }

    /**
     * Список товара(ов) с фильтром
     *
     * @param string $query
     * @param int $limit
     * @param int $page
     * @return array
     * @throws ConnectionException
     */
    public function searchProducts(string $query, int $limit = 30, int $page = 0): array
    {
        $response = $this->getHttpClient()->get("{$this->baseUrl}/products/search", [
            'q' => $query,
            'limit' => $limit,
            'skip' => $page
        ]);

        if (!$response->successful()) {
            return [];
        }

        return $response->json();
    }
}
