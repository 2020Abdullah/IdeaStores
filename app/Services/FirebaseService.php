<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FirebaseService
{
    protected $baseUrl;
    protected $authKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.firebase.database_url'), '/') . '/';
        $this->authKey = config('services.firebase.auth_key'); // من ملف config/services.php
    }

    protected function endpoint($path)
    {
        return "{$this->baseUrl}{$path}.json?auth={$this->authKey}";
    }

    public function create(string $collection, array $data)
    {
        $response = Http::post($this->endpoint($collection), $data)->json();
        return $response['name'] ?? null;
    }

    public function update(string $collectionWithId, array $data)
    {
        return Http::patch($this->endpoint($collectionWithId), $data)->json();
    }

    public function delete(string $collectionWithId)
    {
        return Http::delete($this->endpoint($collectionWithId))->json();
    }

    public function get(string $collection)
    {
        return Http::get($this->endpoint($collection))->json();
    }

    public function getById(string $collection, string $id)
    {
        return Http::get($this->endpoint("$collection/$id"))->json();
    }
}
