<?php

namespace App\Services\Xendit;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XenditClient
{
    protected string $baseUrl;
    protected ?string $secretKey;

    public function __construct()
    {
        $this->baseUrl = config('xendit.base_url', 'https://api.xendit.co');
        $this->secretKey = config('xendit.secret_key');
    }

    public function isConfigured(): bool
    {
        return !empty($this->secretKey);
    }

    public function createInvoice(array $params): array
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Xendit is not configured. Please set XENDIT_SECRET_KEY in .env');
        }

        $response = Http::withBasicAuth($this->secretKey, '')
            ->post("{$this->baseUrl}/v2/invoices", $params);

        if (!$response->successful()) {
            Log::channel('payment')->error('Xendit create invoice failed', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            throw new \Exception('Failed to create invoice: ' . ($response->json()['message'] ?? 'Unknown error'));
        }

        return $response->json();
    }

    public function getInvoice(string $invoiceId): array
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Xendit is not configured');
        }

        $response = Http::withBasicAuth($this->secretKey, '')
            ->get("{$this->baseUrl}/v2/invoices/{$invoiceId}");

        return $response->json();
    }

    public function expireInvoice(string $invoiceId): array
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Xendit is not configured');
        }

        $response = Http::withBasicAuth($this->secretKey, '')
            ->post("{$this->baseUrl}/invoices/{$invoiceId}/expire!");

        return $response->json();
    }

    public function getBalance(): array
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Xendit is not configured');
        }

        $response = Http::withBasicAuth($this->secretKey, '')
            ->get("{$this->baseUrl}/balance");

        return $response->json();
    }

    public function createDisbursement(array $params): array
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Xendit is not configured');
        }

        $response = Http::withBasicAuth($this->secretKey, '')
            ->post("{$this->baseUrl}/disbursements", $params);

        return $response->json();
    }

    public function getDisbursement(string $disbursementId): array
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Xendit is not configured');
        }

        $response = Http::withBasicAuth($this->secretKey, '')
            ->get("{$this->baseUrl}/disbursements/{$disbursementId}");

        return $response->json();
    }

    public function verifyWebhookSignature(string $callbackToken, string $expectedToken): bool
    {
        return hash_equals($expectedToken, $callbackToken);
    }
}
