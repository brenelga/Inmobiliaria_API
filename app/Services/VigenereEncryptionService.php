<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class VigenereEncryptionService
{
    protected $apiUrl;

    public function __construct()
    {
        $this->apiUrl = env('PYTHON_API_URL', 'http://localhost:8800');
    }

    public function encrypt($text, $key)
    {
        $response = Http::post("{$this->apiUrl}/vigenere/cifrar", [
            'texto' => $text,
            'clave' => $key
        ]);

        if ($response->successful()) {
            return $response->json()['resultado'];
        }

        throw new \Exception("Error al cifrar con Vigenère: " . $response->body());
    }

    public function decrypt($text, $key)
    {
        $response = Http::post("{$this->apiUrl}/vigenere/descifrar", [
            'texto' => $text,
            'clave' => $key
        ]);

        if ($response->successful()) {
            return $response->json()['resultado'];
        }

        throw new \Exception("Error al descifrar con Vigenère: " . $response->body());
    }
}