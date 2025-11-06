<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    private static $apiUrl = 'https://api.starsender.online/api/';

    private static function sendRequest($endpoint, $data): Response
    {
        logger()->info('Sending request to WhatsApp API', [
            'endpoint' => $endpoint,
            'data' => $data,
        ]);
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => env('WHATSAPP_API_KEY'),
        ])->post(self::$apiUrl . $endpoint, $data);

        logger()->info('WhatsApp API response received', [
            'endpoint' => $endpoint,
            'data' => $data,
            'response' => $response->body(),
        ]);

        return $response;
    }

    public static function sendGroupMessage($groupId, $message)
    {
        // dd($groupId, $message);
        $data = [
            'To' => $groupId,
            'Body' => $message,
            'MessageType' => 'text',
        ];
        
        return self::sendRequest('send/grup', $data);
    }

    public static function isValidNumber($number): bool
    {
        $data = ['number' => $number];
        $response = self::sendRequest('check-number', $data);
        return $response->json()['data']['status'];
    }

    public static function sendMessage($to, $message)
    {
        $data = [
            'To' => $to,
            'Body' => $message,
            'MessageType' => 'text',
        ];
        
        return self::sendRequest('send-message', $data);
    }
}
