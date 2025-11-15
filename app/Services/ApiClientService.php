<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

/**
 * Simple HTTP API client service used by controllers to call external APIs.
 *
 * Usage:
 *  - inject via constructor: public function __construct(ApiClientService $api)
 *  - call: $this->api->get('path'); $this->api->post('path', $data);
 */
class ApiClientService
{
    protected string $baseUrl;
    protected ?string $token;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.api.url', env('API_URL', '')), '/');
        $this->token = config('services.api.token', env('API_TOKEN')) ?: null;
    }

    protected function client()
    {
        $client = Http::withHeaders([
            'Accept' => 'application/json',
        ])->withOptions(['timeout' => 30]);

        if ($this->token) {
            $client = $client->withToken($this->token);
        }

        return $client;
    }

    protected function url(string $uri): string
    {
        $uri = ltrim($uri, '/');
        return $this->baseUrl === '' ? $uri : ($this->baseUrl . '/' . $uri);
    }

    public function get(string $uri, array $query = [])
    {
        $response = $this->client()->get($this->url($uri), $query);
        try {
            $response->throw();
            return $response->json();
        } catch (RequestException $e) {
            // return consistent error shape for controllers to handle
            return [
                'success' => false,
                'status' => $response->status(),
                'body' => $response->body(),
            ];
        }
    }

    public function post(string $uri, array $data = [])
    {
        $response = $this->client()->post($this->url($uri), $data);
        try {
            $response->throw();
            return $response->json();
        } catch (RequestException $e) {
            return [
                'success' => false,
                'status' => $response->status(),
                'body' => $response->body(),
            ];
        }
    }

    public function put(string $uri, array $data = [])
    {
        $response = $this->client()->put($this->url($uri), $data);
        try {
            $response->throw();
            return $response->json();
        } catch (RequestException $e) {
            return [
                'success' => false,
                'status' => $response->status(),
                'body' => $response->body(),
            ];
        }
    }

    public function delete(string $uri, array $data = [])
    {
        $response = $this->client()->delete($this->url($uri), $data);
        try {
            $response->throw();
            return $response->json();
        } catch (RequestException $e) {
            return [
                'success' => false,
                'status' => $response->status(),
                'body' => $response->body(),
            ];
        }
    }
}
