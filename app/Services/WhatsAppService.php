<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    private $apiUrl;
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        logger()->info('WhatsAppService initialized.');

        // Initialize any required properties or services here
        $this->apiUrl = 'https://api.starsender.online/api/';
    }

    private function sendRequest($endpoint, $data): bool
    {
        logger()->info('Sending request to WhatsApp API', [
            'endpoint' => $endpoint,
            'data' => $data,
        ]);
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => env('WHATSAPP_API_KEY'),
        ])->post($this->apiUrl . $endpoint, $data);

        logger()->info('WhatsApp API response received', [
            'endpoint' => $endpoint,
            'data' => $data,
            'response' => $response->body(),
        ]);

        return $response->successful();
    }

    public function sendMessage($to, $message): bool
    {
        // Logic to send a message via WhatsApp API
        return true; // Return true if the message was sent successfully, otherwise false
    }
}
